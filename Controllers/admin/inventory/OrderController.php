<?php
class OrderController extends BaseAdminController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    // Existing methods (unchanged)
    public function orders() {
        $this->view('admin/inventory/order');
    }

    public function recent_order() {
        $this->view('admin/inventory/Order/Recent_order');
    }

    public function order_history() {
        $this->view('admin/inventory/Order/Order_history');
    }

    public function order_pending() {
        $this->view('admin/inventory/Order/Order_pending');
    }

    public function old_order() {
        $this->view('admin/inventory/Order/Older_order');
    }

    public function order_all() {
        try {
            $canceledCount = $this->orderModel->cancelExpiredPendingOrders();
            if ($canceledCount > 0) {
                error_log("Automatically canceled $canceledCount expired pending orders.");
            }
    
            $allOrders = $this->orderModel->getAllOrders();
            $products = $this->orderModel->getAvailableProducts();
            $locations = $this->orderModel->getLocations();
    
            $draftOrders = array_filter($allOrders, function($order) {
                return $order['orderstatus'] === 'Draft';
            });
            $orderedOrders = array_filter($allOrders, function($order) {
                return in_array($order['orderstatus'], ['Pending', 'Ordered']);
            });
            $partialOrders = array_filter($allOrders, function($order) {
                return $order['orderstatus'] === 'Partial';
            });
            $receivedOrders = array_filter($allOrders, function($order) {
                return $order['orderstatus'] === 'Received';
            });
            $closedOrders = array_filter($allOrders, function($order) {
                return in_array($order['orderstatus'], ['Closed', 'Delivered', 'Canceled', 'Returned']);
            });
    
            $data = [
                'allOrders' => $allOrders,
                'draftOrders' => $draftOrders,
                'orderedOrders' => $orderedOrders,
                'partialOrders' => $partialOrders,
                'receivedOrders' => $receivedOrders,
                'closedOrders' => $closedOrders,
                'products' => $products,
                'locations' => $locations
            ];
            $this->view('admin/inventory/Order/Allorder', $data);
        } catch (Exception $e) {
            error_log("Error in order_all: " . $e->getMessage());
            $data = [
                'allOrders' => [],
                'draftOrders' => [],
                'orderedOrders' => [],
                'partialOrders' => [],
                'receivedOrders' => [],
                'closedOrders' => [],
                'products' => [],
                'locations' => []
            ];
            $this->view('admin/inventory/Order/Allorder', $data);
        }
    }

    public function deleteOrder() {
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $orderId = $_POST['orderId'] ?? null;
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'No order ID provided']);
            exit;
        }
        try {
            $this->orderModel->deleteOrder($orderId);
            echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
        } catch (Exception $e) {
            error_log("Error in deleteOrder: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to delete order: ' . $e->getMessage()]);
        }
        exit;
    }

    public function updateOrderStatus() {
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $orderId = $_POST['orderId'] ?? null;
        $newStatus = $_POST['orderStatus'] ?? null;
        if (!$orderId || !is_numeric($orderId) || $orderId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
            exit;
        }
        if (!$newStatus || !in_array($newStatus, ['Pending', 'Delivered', 'Canceled', 'Returned'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid order status']);
            exit;
        }
        try {
            $this->orderModel->updateOrderStatus($orderId, $newStatus);
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
        } catch (Exception $e) {
            error_log("Error in updateOrderStatus: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to update order status: ' . $e->getMessage()]);
        }
        exit;
    }

    //delete order for customer
    public function deleteOrderFromHistory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: /order_h?error=invalid_request_method');
            exit;
        }

        $orderId = $_POST['orderId'] ?? null;
        if (!$orderId){
            header('Location: /order_h?error=no_id');
            exit;
        }
        try{
            $this->orderModel->deleteOrder($orderId);
            header('Location: /order_h');
            exit;
        }catch (Exception $e){
            error_log("Error in deleteOrderFromHistory:". $e->getMessage());
            header('Location: /order_h?error=delete_failed');
            exit;
        }
    }
    

    public function cancelOrder() {
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $orderId = $_POST['orderId'] ?? null;
        if (!$orderId || !is_numeric($orderId) || $orderId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
            exit;
        }
        try {
            $this->orderModel->cancelOrder($orderId);
            echo json_encode(['success' => true, 'message' => 'Order canceled successfully']);
        } catch (Exception $e) {
            error_log("Error in cancelOrder: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to cancel order: ' . $e->getMessage()]);
        }
        exit;
    }

    public function cancelExpiredOrders() {
        try {
            $canceledCount = $this->orderModel->cancelExpiredPendingOrders();
            if ($canceledCount > 0) {
                error_log("Automatically canceled $canceledCount expired pending orders.");
                echo "Successfully canceled $canceledCount expired pending orders.";
            } else {
                echo "No expired pending orders found.";
            }
        } catch (Exception $e) {
            error_log("Error in cancelExpiredOrders: " . $e->getMessage());
            echo "Failed to cancel expired orders: " . $e->getMessage();
        }
        exit;
    }

    // New method for bulk cancel
    public function bulkCancelOrders() {
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $orderIds = json_decode($_POST['orderIds'] ?? '[]', true);
        if (empty($orderIds)) {
            echo json_encode(['success' => false, 'message' => 'No order IDs provided']);
            exit;
        }
        try {
            $canceledCount = 0;
            foreach ($orderIds as $orderId) {
                if (is_numeric($orderId) && $orderId > 0) {
                    try {
                        $this->orderModel->cancelOrder($orderId);
                        $canceledCount++;
                    } catch (Exception $e) {
                        // Log individual errors but continue processing others
                        error_log("Failed to cancel order $orderId: " . $e->getMessage());
                    }
                }
            }
            echo json_encode([
                'success' => true,
                'message' => "Successfully canceled $canceledCount order(s)",
                'canceledCount' => $canceledCount
            ]);
        } catch (Exception $e) {
            error_log("Error in bulkCancelOrders: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to cancel orders: ' . $e->getMessage()]);
        }
        exit;
    }

    // New method for bulk delete
    public function bulkDeleteOrders() {
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $orderIds = json_decode($_POST['orderIds'] ?? '[]', true);
        if (empty($orderIds)) {
            echo json_encode(['success' => false, 'message' => 'No order IDs provided']);
            exit;
        }
        try {
            $deletedCount = 0;
            foreach ($orderIds as $orderId) {
                if (is_numeric($orderId) && $orderId > 0) {
                    try {
                        $this->orderModel->deleteOrder($orderId);
                        $deletedCount++;
                    } catch (Exception $e) {
                        // Log individual errors but continue processing others
                        error_log("Failed to delete order $orderId: " . $e->getMessage());
                    }
                }
            }
            echo json_encode([
                'success' => true,
                'message' => "Successfully deleted $deletedCount order(s)",
                'deletedCount' => $deletedCount
            ]);
        } catch (Exception $e) {
            error_log("Error in bulkDeleteOrders: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to delete orders: ' . $e->getMessage()]);
        }
        exit;
    }

    // create order
    public function createOrder() {
        // header('Content-Type: application/json; charset=UTF-8');
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $products = $this->orderModel->getAvailableProducts();
                $locations = $this->orderModel->getLocations();
                $data = [
                    'products' => $products,
                    'locations' => $locations
                ];
                $this->view('admin/inventory/Order/CreateOrder', $data);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productId = $_POST['productId'] ?? null;
                $quantity = $_POST['quantity'] ?? null;
                $locationId = $_POST['locationId'] ?? null;
                $price = $_POST['price'] ?? null;
    
                // Validate inputs
                if (!$productId || !is_numeric($productId) || $productId <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                    exit;
                }
                if (!$quantity || !is_numeric($quantity) || $quantity <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
                    exit;
                }
                if (!$locationId || !is_numeric($locationId) || $locationId <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid destination']);
                    exit;
                }
                if (!$price || !is_numeric($price) || $price < 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid price']);
                    exit;
                }
    
                // Fetch product
                $product = $this->orderModel->getProductById($productId);
                if (!$product) {
                    echo json_encode(['success' => false, 'message' => 'Product not found']);
                    exit;
                }
                if ($product['stockquantity'] < $quantity) {
                    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
                    exit;
                }
    
                // Calculate total price
                $totalPrice = $price * $quantity;
                $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Use session or default
                $paymentMethodId = 1; // Default payment method ID (adjust as needed)
                $currency = 'USD'; // Default currency (adjust as needed)
                $orderStatus = 'Pending';
    
                // Create order
                $orderId = $this->orderModel->createOrder($userId, $paymentMethodId, $locationId, $totalPrice, $orderStatus, $currency);
                if ($orderId) {
                    // Add order item
                    $this->orderModel->addOrderItem($orderId, $productId, $quantity);
                    // Update product stock
                    if ($this->orderModel->updateProductStock($productId, $product['stockquantity'] - $quantity)) {
                        echo json_encode(['success' => true, 'message' => 'Order created successfully']);
                    } else {
                        // Rollback order creation
                        $this->orderModel->deleteOrder($orderId);
                        echo json_encode(['success' => false, 'message' => 'Failed to update product stock']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to create order in database']);
                }
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                exit;
            }
        } catch (Exception $e) {
            error_log("Error in createOrder: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
            exit;
        }
    }

    // for chat controller
    // public function sendMessage() {
    //     header('Content-Type: application/json; charset=UTF-8');
    //     try {
    //         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //             echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    //             exit;
    //         }

    //         $orderId = $_POST['orderId'] ?? null;
    //         $messageContent = $_POST['messageContent'] ?? null;
    //         $senderId = $_SESSION['user_id'] ?? null;

    //         if (!$senderId) {
    //             echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    //             exit;
    //         }
    //         if (!$messageContent || trim($messageContent) === '') {
    //             echo json_encode(['success' => false, 'message' => 'Message content is required']);
    //             exit;
    //         }
    //         if ($orderId && (!is_numeric($orderId) || $orderId <= 0)) {
    //             echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    //             exit;
    //         }

    //         // Determine if sender is admin by querying the users table
    //         $user = $this->orderModel->getUserById($senderId);
    //         if (!$user || !isset($user['role'])) {
    //             echo json_encode(['success' => false, 'message' => 'User role not found']);
    //             exit;
    //         }
    //         $isAdmin = $user['role'] === 'admin';
    //         error_log("Sender ID: $senderId, Is Admin: " . ($isAdmin ? 'true' : 'false') . ", Order ID: $orderId");

    //         if ($isAdmin) {
    //             // Admin to customer: Send one message to the customer
    //             $order = $this->orderModel->getOrderById($orderId);
    //             if (!$order || !isset($order[0]['user_id'])) {
    //                 echo json_encode(['success' => false, 'message' => 'Customer not found for this order']);
    //                 exit;
    //             }
    //             $receiverId = $order[0]['user_id'];
    //             if ($receiverId == $senderId) {
    //                 echo json_encode(['success' => false, 'message' => 'Cannot send message to self']);
    //                 exit;
    //             }
    //             error_log("Receiver ID: $receiverId");
    //             $this->orderModel->saveMessage($orderId, $senderId, $receiverId, $messageContent);
    //         } else {
    //             // Customer to all admins: Create a message for each admin
    //             $admins = $this->orderModel->getAllAdmins();
    //             if (empty($admins)) {
    //                 echo json_encode(['success' => false, 'message' => 'No admins available']);
    //                 exit;
    //             }
    //             foreach ($admins as $admin) {
    //                 $receiverId = $admin['id'];
    //                 if ($receiverId == $senderId) {
    //                     continue; // Skip if the admin is the sender (unlikely)
    //                 }
    //                 error_log("Receiver ID: $receiverId");
    //                 $this->orderModel->saveMessage($orderId, $senderId, $receiverId, $messageContent);
    //             }
    //         }

    //         echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
    //         exit;
    //     } catch (Exception $e) {
    //         error_log("Error in sendMessage: " . $e->getMessage());
    //         echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    //         exit;
    //     }
    // }
    public function sendMessage() {
    header('Content-Type: application/json; charset=UTF-8');
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $orderId = $_POST['orderId'] ?? null;
        $messageContent = $_POST['messageContent'] ?? null;
        $senderId = $_SESSION['user_id'] ?? null;

        if (!$senderId) {
            echo json_encode(['success' => false, 'message' => 'User not authenticated']);
            exit;
        }
        if (!$messageContent || trim($messageContent) === '') {
            echo json_encode(['success' => false, 'message' => 'Message content is required']);
            exit;
        }
        if ($orderId && (!is_numeric($orderId) || $orderId <= 0)) {
            echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
            exit;
        }

        // Get sender info
        $user = $this->orderModel->getUserById($senderId);
        if (!$user || !isset($user['role'])) {
            echo json_encode(['success' => false, 'message' => 'User role not found']);
            exit;
        }
        $isAdmin = $user['role'] === 'admin';
        $senderUsername = $user['username'] ?? 'Unknown';

        if ($isAdmin) {
            $order = $this->orderModel->getOrderById($orderId);
            if (!$order || !isset($order[0]['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Customer not found for this order']);
                exit;
            }
            $receiverId = $order[0]['user_id'];
            if ($receiverId == $senderId) {
                echo json_encode(['success' => false, 'message' => 'Cannot send message to self']);
                exit;
            }

            $this->orderModel->saveMessage($orderId, $senderId, $receiverId, $messageContent);
        } else {
            $admins = $this->orderModel->getAllAdmins();
            if (empty($admins)) {
                echo json_encode(['success' => false, 'message' => 'No admins available']);
                exit;
            }

            foreach ($admins as $admin) {
                $receiverId = $admin['id'];
                if ($receiverId == $senderId) {
                    continue;
                }
                $this->orderModel->saveMessage($orderId, $senderId, $receiverId, $messageContent);
            }

            // âœ… Send to Telegram after customer sends message
            $this->sendTelegramChatMessage($senderUsername, $messageContent, $orderId);
        }

        echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
        exit;
    } catch (Exception $e) {
        error_log("Error in sendMessage: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}

    // get messages for chat controller
    public function getMessages() {
        header('Content-Type: application/json; charset=UTF-8');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                exit;
            }
    
            $orderId = $_GET['orderId'] ?? null;
            $userId = $_SESSION['user_id'] ?? null;
    
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'User not authenticated']);
                exit;
            }
            if ($orderId && (!is_numeric($orderId) || $orderId <= 0)) {
                echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
                exit;
            }
    
            $messages = $this->orderModel->getMessagesByOrder($orderId, $userId);
            
            echo json_encode(['success' => true, 'messages' => $messages]);
            exit;
        } catch (Exception $e) {
            error_log("Error in getMessages: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
            exit;
        }
    }

    private function sendTelegramMessage($order) {
        $botToken = '8388937134:AAGooYX9MzhqzCG4OA9wyPpTSTFPOgpz668';
        $chatId = '1461253065';

        $message = "<b>ðŸ›’ New Order Placed</b>\n";
        $message .= "ðŸ†” <b>Order ID:</b> " . $order['order_id'] . "\n";
        $message .= "ðŸ‘¤ <b>User ID:</b> " . $order['user_id'] . "\n";
        $message .= "ðŸ’° <b>Total:</b> " . number_format($order['totalprice'], 2) . " " . strtoupper($order['payments']) . "\n";
        $message .= "ðŸ“ <b>Location ID:</b> " . $order['location_id'] . "\n";
        $message .= "ðŸ“¦ <b>Status:</b> " . $order['orderstatus'] . "\n";
        $message .= "ðŸ•’ <b>Date:</b> " . date('M d, Y H:i');

        $url = "https://api.telegram.org/bot$botToken/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];

        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ];

        file_get_contents($url, false, stream_context_create($options));
    }

    // Send chat message to Telegram
    private function sendTelegramChatMessage($username, $messageContent, $orderId) {
        $botToken = '8388937134:AAGooYX9MzhqzCG4OA9wyPpTSTFPOgpz668';
        $chatId = '1461253065';

        $message = "<b>ðŸ’¬ New Chat Message</b>\n";
        $message .= "ðŸ‘¤ <b>Sender:</b> " . htmlspecialchars($username) . "\n";
        $message .= "ðŸ†” <b>Order ID:</b> " . htmlspecialchars($orderId) . "\n";
        $message .= "ðŸ’¬ <b>Message:</b> " . htmlspecialchars($messageContent) . "\n";
        $message .= "ðŸ•’ <b>Date:</b> " . date('M d, Y H:i');

        $url = "https://api.telegram.org/bot$botToken/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];

        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ];

        file_get_contents($url, false, stream_context_create($options));
    }


}
?>