<?php
require_once "Models/OrderModel.php";
require_once "Models/PaymentModelF.php";
require_once "Models/TransactionModel.php";
require_once "Models/ProductModel.php";
require_once "Models/StockModel.php"; // Add this new dependency

class payController extends BasecustomerController {
    private $orderModel;
    private $paymentModelF;
    private $transactionModel;
    private $productModel;
    private $stockModel; // Add StockModel property

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->orderModel = new OrderModel();
        $this->paymentModelF = new PaymentModelF();
        $this->transactionModel = new TransactionModel();
        $this->productModel = new ProductModel();
        $this->stockModel = new StockModel(); // Initialize StockModel
    }

    // Confirm payment page (unchanged)
    public function index() {
        $this->verifyUserLoggedIn();
        $order_id = $_GET['order_id'] ?? null;

        if (!$order_id) {
            $this->view('pages/payment/confirmPayment', ['error' => 'No order specified']);
            return;
        }

        $orderDetails = $this->orderModel->getOrderById($order_id);
        if (empty($orderDetails)) {
            $this->view('pages/payment/confirmPayment', ['error' => 'Order not found']);
            return;
        }

        $order = $orderDetails[0];
        $payment_method = null;
        $payment_type = 'Unknown';

        if (isset($_SESSION['confirm_data']) && $_SESSION['confirm_data']['order_id'] == $order_id) {
            $payment_type = $_SESSION['confirm_data']['payment_type'] ?? 'Unknown';
        } else {
            if ($order['payment_method_id']) {
                $payment_method = $this->paymentModelF->getPaymentMethodById($order['payment_method_id']);
                if ($payment_method) {
                    $payment_type = $this->guessPaymentType($payment_method['card_number']);
                } else {
                    error_log("Payment method not found for payment_method_id: " . $order['payment_method_id']);
                }
            } else {
                error_log("No payment method associated with order_id: $order_id");
            }
        }

        error_log("payController::index - Order ID: $order_id, Payment Type: $payment_type");

        $this->view('pages/payment/confirmPayment', [
            'order' => $order,
            'orderItems' => $orderDetails,
            'payment_method' => $payment_method,
            'currency' => $order['payments'],
            'payment_type' => $payment_type
        ]);
    }

    // Confirm payment submission (add new logic without changing old)
    public function confirmPayment() {
        $this->verifyUserLoggedIn();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/confirmpayment');
            return;
        }

        $order_id = $_POST['order_id'] ?? null;
        $amount = floatval($_POST['amount'] ?? 0);
        $payment_type = $_POST['payment_type'] ?? 'Unknown';

        if (!$order_id || $amount <= 0) {
            $this->redirect('/confirmpayment?order_id=' . $order_id . '&error=Invalid order or amount');
            return;
        }

        try {
            $orderDetails = $this->orderModel->getOrderById($order_id);
            if (empty($orderDetails)) {
                $this->redirect('/confirmpayment?order_id=' . $order_id . '&error=Order not found');
                return;
            }

            $order = $orderDetails[0];

            error_log("payController::confirmPayment - Order ID: $order_id, Amount: $amount, Payment Type: $payment_type");

            // Existing stock update logic (unchanged)
            foreach ($orderDetails as $item) {
                $product_id = $item['product_id'];
                $quantity_ordered = intval($item['quantity']);

                $product = $this->productModel->getProductById($product_id);
                if (!$product) {
                    throw new Exception("Product not found: product_id=$product_id");
                }

                $current_stock = intval($product['stockquantity']);
                if ($current_stock < $quantity_ordered) {
                    throw new Exception("Insufficient stock for product_id=$product_id. Available: $current_stock, Ordered: $quantity_ordered");
                }

                $new_stock = $current_stock - $quantity_ordered;

                $this->productModel->updateProduct(
                    $product['productname'],
                    $product['descriptions'],
                    $product['categories'],
                    $product['price'],
                    $new_stock,
                    $product['imageURL'],
                    $product_id
                );

                error_log("Stock updated for product_id=$product_id: $current_stock - $quantity_ordered = $new_stock");

                // NEW: Increment stockOut in inventory table
                $this->stockModel->incrementStockOut($product_id, $quantity_ordered);
            }

            // Existing transaction logic (unchanged)
            $this->transactionModel->createTransaction($order_id, $payment_type, $amount);

            // Clear session data (unchanged)
            unset($_SESSION['confirm_data']);

            $this->redirect('/orderSuccess?order_id=' . $order_id);
        } catch (Exception $e) {
            error_log("Error confirming payment or updating stock: " . $e->getMessage());
            $this->redirect('/confirmpayment?order_id=' . $order_id . '&error=Error confirming payment or updating stock: ' . $e->getMessage());
        }
    }

    // Order success page (unchanged)
    public function OrderSuccess() {
        $this->verifyUserLoggedIn();
        $order_id = $_GET['order_id'] ?? null;

        if (!$order_id) {
            $this->view('pages/payment/ordersuccess', ['error' => 'No order specified']);
            return;
        }

        $orderDetails = $this->orderModel->getOrderById($order_id);
        if (empty($orderDetails)) {
            $this->view('pages/payment/ordersuccess', ['error' => 'Order not found']);
            return;
        }

        $this->view('pages/payment/ordersuccess', [
            'order' => $orderDetails[0],
            'orderItems' => $orderDetails
        ]);
    }

    private function verifyUserLoggedIn() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/F_login');
        }
    }

    private function guessPaymentType($card_number) {
        if (empty($card_number)) {
            return 'Unknown';
        }
        $card_number = preg_replace('/\s+/', '', $card_number);
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $card_number)) {
            return 'Visa';
        } elseif (preg_match('/^5[1-5][0-9]{14}$/', $card_number)) {
            return 'MasterCard';
        } elseif (preg_match('/^3[47][0-9]{13}$/', $card_number)) {
            return 'Amex';
        } else {
            return 'Unknown';
        }
    }
}