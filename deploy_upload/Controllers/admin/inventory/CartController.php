<?php
require_once "Models/ProductModel.php";
require_once "Models/cartModel.php";
require_once "Models/UserModel.php";
require_once "Models/LocationModel.php";
require_once "Models/PaymentModelF.php";
require_once "Models/OrderModel.php";

class CartController extends BasecustomerController {
    private $cartModel;
    private $productModel;
    private $userModel;
    private $locationModel;
    private $paymentModelF;
    private $orderModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
        $this->locationModel = new LocationModel();
        $this->paymentModelF = new PaymentModelF();
        $this->orderModel = new OrderModel();
    }

    public function add() {
        $this->verifyUserLoggedIn();
        $user_id = $_SESSION['user_id'];
        $product_id = $this->getPost('product_id');
        $quantity = $this->getPost('quantity', 1);

        error_log("Adding to cart: user_id=$user_id, product_id=$product_id, quantity=$quantity");

        if ($quantity < 1) {
            error_log("Quantity validation failed");
            $this->jsonResponse(false, 'Quantity must be at least 1');
        }

        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            error_log("Product not found: product_id=$product_id");
            $this->jsonResponse(false, 'Product not found');
        }

        if ($product['stockquantity'] < $quantity) {
            error_log("Insufficient stock: product_id=$product_id, stock={$product['stockquantity']}, requested=$quantity");
            $this->jsonResponse(false, 'Not enough stock available');
        }

        try {
            $success = $this->cartModel->addItem($user_id, $product_id, $quantity);
            if ($success) {
                error_log("Successfully added to cart: user_id=$user_id, product_id=$product_id");
            } else {
                error_log("Failed to add to cart: user_id=$user_id, product_id=$product_id");
            }
            $this->jsonResponse($success, $success ? 'Item added to cart' : 'Failed to add item');
        } catch (Exception $e) {
            error_log("Exception while adding to cart: " . $e->getMessage());
            $this->jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    public function getItems() {
        $this->verifyUserLoggedIn();
        $user_id = $_SESSION['user_id'];
        try {
            $items = $this->cartModel->getCartItems($user_id);
            error_log("Fetched cart items for user_id=$user_id, items_count=" . count($items));
            $this->jsonResponse(true, 'Cart items retrieved', $items);
        } catch (Exception $e) {
            error_log("Exception while fetching cart items: " . $e->getMessage());
            $this->jsonResponse(false, 'Error fetching cart items: ' . $e->getMessage());
        }
    }

    public function remove() {
        $this->verifyUserLoggedIn();
        $user_id = $_SESSION['user_id'];
        $product_id = $this->getPost('product_id');
        
        try {
            $success = $this->cartModel->removeItem($user_id, $product_id);
            if ($success) {
                error_log("Successfully removed from cart: user_id=$user_id, product_id=$product_id");
            } else {
                error_log("Failed to remove from cart: user_id=$user_id, product_id=$product_id");
            }
            $this->jsonResponse($success, $success ? 'Item removed from cart' : 'Failed to remove item');
        } catch (Exception $e) {
            error_log("Exception while removing from cart: " . $e->getMessage());
            $this->jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    private function verifyUserLoggedIn() {
        if (!isset($_SESSION['user_id'])) {
            error_log("User not logged in");
            $this->jsonResponse(false, 'User not logged in');
        }
    }

    private function getPost($key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    private function jsonResponse($success, $message = '', $data = []) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public function viewcart() {
        $this->verifyUserLoggedIn();
        $user_id = $_SESSION['user_id'];
        try {
            $cartItems = $this->cartModel->getCartItems($user_id);
            error_log("Fetched cart items for user_id=$user_id, items_count=" . count($cartItems));
            $this->view('pages/payment/viewcart', ['cartItems' => $cartItems]);
        } catch (Exception $e) {
            error_log("Exception while fetching cart items: " . $e->getMessage());
            $this->view('pages/payment/viewcart', ['cartItems' => [], 'error' => 'Error fetching cart items']);
        }
    }

    public function checkout() {
        $this->verifyUserLoggedIn();
        $user_id = $_SESSION['user_id'];
    
        try {
            $cartItems = $this->cartModel->getCartItems($user_id);
            if (empty($cartItems)) {
                $this->view('pages/payment/checkout', ['cartItems' => [], 'error' => 'Cart is empty']);
                return;
            }
    
            $user = $this->userModel->getUserById($user_id);
            $locations = $this->locationModel->getAllLocations();
            error_log("CartController::checkout - Fetched locations: " . print_r($locations, true));
    
            $this->view('pages/payment/checkout', [
                'cartItems' => $cartItems,
                'user' => $user,
                'locations' => $locations
            ]);
        } catch (Exception $e) {
            error_log("Exception in checkout: " . $e->getMessage());
            $this->view('pages/payment/checkout', [
                'cartItems' => [],
                'error' => 'Error loading checkout: ' . $e->getMessage()
            ]);
        }
    }

    public function process() {
        $this->verifyUserLoggedIn();
        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Invalid request method');
        }

        $cart_items = $_POST['cart_items'] ?? [];
        $total_price = floatval($_POST['total_price'] ?? 0); // This includes shipping from checkout.php
        $currency = $_POST['currency'] ?? 'USD';
        $location_id = intval($_POST['location_id'] ?? 0);
        $order_status = $_POST['order_status'] ?? 'Pending';
        $payment_method = $_POST['payment_method'] ?? 'Unknown';
        $card_holder_name = $_POST['card_holder_name'] ?? '';
        $card_number = $_POST['card_number'] ?? '';
        $expiry_date = $_POST['expiry_date'] ?? '';
        $cvv = $_POST['cvv'] ?? '';

        error_log("CartController::process - Inputs: payment_method=$payment_method, card_number=$card_number, card_holder_name=$card_holder_name, expiry_date=$expiry_date, cvv=$cvv, currency=$currency");

        if (empty($cart_items) || $total_price <= 0 || empty($location_id)) {
            $this->jsonResponse(false, 'Missing required fields');
        }

        if ($payment_method !== 'Paypal' && (empty($card_holder_name) || empty($card_number) || empty($expiry_date) || empty($cvv))) {
            $this->jsonResponse(false, 'Missing payment details for card payment');
        }

        // Calculate subtotal from cart_items (excluding shipping)
        $applied_coupon = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;
        $subtotal = 0;
        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity = intval($item['quantity']);
            $product = $this->productModel->getProductById($product_id);
            if ($product) {
                $price = floatval($product['price']);
                $discounted_price = $this->getDiscountedPrice($price, $applied_coupon);
                $subtotal += $discounted_price * $quantity;
            }
        }

        try {
            $payment_method_id = null;
            $payment_method_details = null;

            if ($payment_method !== 'Paypal') {
                $payment_method_id = $this->paymentModelF->savePaymentMethod($user_id, $card_number, $card_holder_name, $expiry_date, $cvv, $currency, $payment_method);
                $payment_method_details = [
                    'card_holder_name' => $card_holder_name,
                    'card_number' => $card_number,
                    'expiry_date' => $expiry_date,
                    'cvv' => $cvv
                ];
            } else {
                $payment_method_id = $this->paymentModelF->savePaymentMethod($user_id, null, null, null, null, $currency, 'Paypal');
                $payment_method_details = null;
            }

            // Use subtotal instead of total_price for the order
            $order_id = $this->orderModel->createOrder($user_id, $payment_method_id, $location_id, $subtotal, $order_status, $currency);

            foreach ($cart_items as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $this->orderModel->addOrderItem($order_id, $product_id, $quantity);
            }

            $this->cartModel->clearCart($user_id);

            $_SESSION['confirm_data'] = [
                'order_id' => $order_id,
                'currency' => $currency,
                'payment_type' => $payment_method,
                'payment_method' => $payment_method_details
            ];

            $this->jsonResponse(true, 'Order created successfully', ['order_id' => $order_id]);
        } catch (Exception $e) {
            error_log("Error processing checkout: " . $e->getMessage());
            $this->jsonResponse(false, 'Error processing order: ' . $e->getMessage());
        }
    }

    // Add getDiscountedPrice method to calculate discounts consistently
    private function getDiscountedPrice($price, $coupon) {
        if (!$coupon) return $price;
        if ($coupon['discount_type'] === 'percentage') {
            return $price * (1 - $coupon['discount_value'] / 100);
        } else {
            return max(0, $price - $coupon['discount_value']);
        }
    }

    public function update() {
        $this->verifyUserLoggedIn();
        $user_id = $_SESSION['user_id'];
        $product_id = $this->getPost('product_id');
        $quantity = $this->getPost('quantity', 1);
    
        error_log("Updating cart: user_id=$user_id, product_id=$product_id, quantity=$quantity");
    
        if ($quantity < 1) {
            error_log("Quantity validation failed");
            $this->jsonResponse(false, 'Quantity must be at least 1');
        }
    
        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            error_log("Product not found: product_id=$product_id");
            $this->jsonResponse(false, 'Product not found');
        }
    
        if ($product['stockquantity'] < $quantity) {
            error_log("Insufficient stock: product_id=$product_id, stock={$product['stockquantity']}, requested=$quantity");
            $this->jsonResponse(false, 'Not enough stock available');
        }
    
        try {
            $success = $this->cartModel->updateItem($user_id, $product_id, $quantity);
            error_log("Update result: " . ($success ? 'true' : 'false'));
            if ($success) {
                error_log("Successfully updated cart: user_id=$user_id, product_id=$product_id");
            } else {
                error_log("Failed to update cart: user_id=$user_id, product_id=$product_id");
            }
            $this->jsonResponse($success, $success ? 'Quantity updated' : 'Failed to update quantity');
        } catch (Exception $e) {
            error_log("Exception while updating cart: " . $e->getMessage());
            $this->jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    public function applyCoupon() {
        $this->verifyUserLoggedIn();
        $coupon_code = $_POST['coupon_code'] ?? '';
        $user_id = $_SESSION['user_id'];
    
        if (empty($coupon_code)) {
            unset($_SESSION['applied_coupon']);
            echo json_encode(['success' => true, 'message' => 'Coupon cleared']);
            exit();
        }
    
        $coupon = $this->productModel->validateCoupon($coupon_code, $user_id);
        if ($coupon) {
            $expiry_date = $this->productModel->getCouponExpiry($coupon['id']);
            if ($expiry_date && strtotime($expiry_date) < time()) {
                unset($_SESSION['applied_coupon']);
                echo json_encode(['success' => false, 'message' => 'Coupon has expired']);
                exit();
            }
    
            $_SESSION['applied_coupon'] = [
                'code' => $coupon_code,
                'discount_type' => $coupon['discount_type'],
                'discount_value' => $coupon['discount_value'],
                'expiry_date' => $expiry_date
            ];
            $this->productModel->incrementCouponUsage($coupon['id'], $user_id);
    
            echo json_encode([
                'success' => true,
                'discount_type' => $coupon['discount_type'],
                'discount_value' => $coupon['discount_value']
            ]);
        } else {
            unset($_SESSION['applied_coupon']);
            echo json_encode(['success' => false, 'message' => 'Invalid or unauthorized coupon code']);
        }
        exit();
    }
}