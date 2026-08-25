<?php
require_once "Models/orderHistoryModel.php";
class orderHistoryController extends BasecustomerController {
    private $orderHistoryModel;

    public function __construct() {
        $this->orderHistoryModel = new OrderHistoryModel();
    }

    public function index() {
        try {
            $user_id = $_SESSION['user_id'] ?? null;
            if (!$user_id) {
                header('Location: /login');
                exit;
            }
            $orders = $this->orderHistoryModel->getOrdersByUserId($user_id);
            $products = $this->orderHistoryModel->getAvailableProducts();
            $data = ['orders' => $orders, 'products' => $products];
            $this->view('pages/order/order_h', $data);
        } catch (Exception $e) {
            error_log("Error in orderHistory index: " . $e->getMessage());
            $data = ['orders' => [], 'products' => []];
            $this->view('pages/order/order_h', $data);
        }
    }

    public function submitChangeRequest() {
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $user_id = $_SESSION['user_id'] ?? null;
        $orderId = $_POST['orderId'] ?? null;
        $requestedProductId = $_POST['requestedProductId'] ?? null;
        $returnReason = $_POST['returnReason'] ?? null;

        if (!$user_id || !$orderId || !$requestedProductId || !$returnReason) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $this->orderHistoryModel->submitChangeRequest($user_id, $orderId, $requestedProductId, $returnReason);
            echo json_encode(['success' => true, 'message' => 'Change request submitted successfully']);
        } catch (Exception $e) {
            error_log("Error in submitChangeRequest: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to submit change request: ' . $e->getMessage()]);
        }
        exit;
    }

    public function processChangeRequest() {
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $orderId = $_POST['orderId'] ?? null;
        $returnAction = $_POST['returnAction'] ?? null;
        $requestedProductId = $_POST['requestedProductId'] ?? null;

        if (!$orderId || !$returnAction || !$requestedProductId) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        if (!in_array($returnAction, ['Approve', 'Reject'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
        }

        try {
            $this->orderHistoryModel->processChangeRequest($orderId, $returnAction, $requestedProductId);
            echo json_encode(['success' => true, 'message' => 'Change request processed successfully']);
        } catch (Exception $e) {
            error_log("Error in processChangeRequest: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to process change request: ' . $e->getMessage()]);
        }
        exit;
    }
}
?>