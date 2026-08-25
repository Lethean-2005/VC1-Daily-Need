<?php
class OrderModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function createOrder($user_id, $payment_method_id, $location_id, $total_price, $order_status, $currency) {
        try {
            $result = $this->db->query(
                "INSERT INTO orders (user_id, payment_method_id, location_id, totalprice, orderstatus, payments, orderdate) 
                VALUES (:user_id, :payment_method_id, :location_id, :totalprice, :orderstatus, :payments, NOW())",
                [
                    'user_id' => $user_id,
                    'payment_method_id' => $payment_method_id,
                    'location_id' => $location_id,
                    'totalprice' => $total_price,
                    'orderstatus' => $order_status,
                    'payments' => $currency
                ]
            );

            $orderId = $this->db->lastInsertId();

            // ✅ Send Telegram Notification
            $this->sendTelegramMessage([
                'order_id' => $orderId,
                'user_id' => $user_id,
                'payment_method_id' => $payment_method_id,
                'location_id' => $location_id,
                'totalprice' => $total_price,
                'orderstatus' => $order_status,
                'payments' => $currency
            ]);

            return $orderId;
        } catch (Exception $e) {
            error_log("Error creating order: " . $e->getMessage());
            throw $e;
        }
    }

        private function sendTelegramMessage($order) {
            $botToken = '8388937134:AAGooYX9MzhqzCG4OA9wyPpTSTFPOgpz668';
            $chatId = '1461253065';

            $message = "<b>🛒 New Order Placed</b>\n";
            $message .= "🆔 <b>Order ID:</b> " . $order['order_id'] . "\n";
            $message .= "👤 <b>User ID:</b> " . $order['user_id'] . "\n";
            $message .= "💰 <b>Total:</b> " . number_format($order['totalprice'], 2) . " " . strtoupper($order['payments']) . "\n";
            $message .= "📍 <b>Location ID:</b> " . $order['location_id'] . "\n";
            $message .= "📦 <b>Status:</b> " . $order['orderstatus'] . "\n";
            $message .= "🕒 <b>Date:</b> " . date('M d, Y H:i');

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

        // Message handling methods
        private function sendTelegramChatMessage($senderName, $messageContent, $orderId = null) {
            $botToken = '8388937134:AAGooYX9MzhqzCG4OA9wyPpTSTFPOgpz668';
            $chatId = '1461253065';

            $message = "<b>💬 New Message from Customer</b>\n";
            $message .= "👤 <b>Sender:</b> " . htmlspecialchars($senderName) . "\n";
            if ($orderId) {
                $message .= "🆔 <b>Order ID:</b> " . $orderId . "\n";
            }
            $message .= "📝 <b>Message:</b> " . htmlspecialchars($messageContent);

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



    public function addOrderItem($order_id, $product_id, $quantity) {
        try {
            $this->db->query(
                "INSERT INTO orderitems (order_id, product_id, quantity) 
                VALUES (:order_id, :product_id, :quantity)",
                [
                    'order_id' => $order_id,
                    'product_id' => $product_id,
                    'quantity' => $quantity
                ]
            );
        } catch (Exception $e) {
            error_log("Error adding order item: " . $e->getMessage());
            throw $e;
        }
    }

    public function getOrderById($order_id) {
        try {
            $result = $this->db->query(
                "SELECT o.*, oi.product_id, oi.quantity, p.productname, p.price, p.imageURL, l.location_name 
                FROM orders o 
                LEFT JOIN orderitems oi ON o.id = oi.order_id 
                LEFT JOIN products p ON oi.product_id = p.id 
                LEFT JOIN locations l ON o.location_id = l.id 
                WHERE o.id = :id",
                ['id' => $order_id]
            );
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching order: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllOrders() {
        try {
            $result = $this->db->query(
                "SELECT o.*, 
                        (SELECT p.productname 
                         FROM products p 
                         JOIN orderitems oi ON p.id = oi.product_id 
                         WHERE oi.order_id = o.id 
                         LIMIT 1) AS product_name, 
                        u.username, 
                        u.profile AS user_profile,
                        r.return_reason AS return_details,
                        r.requested_product_id,
                        r.status AS return_status,
                        l.location_name
                 FROM orders o 
                 LEFT JOIN users u ON o.user_id = u.id 
                 LEFT JOIN returns r ON o.id = r.order_id
                 LEFT JOIN locations l ON o.location_id = l.id
                 ORDER BY o.orderdate ASC"
            );
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching all orders: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAvailableProducts() {
        try {
            $result = $this->db->query(
                "SELECT id, productname, price, stockquantity
                 FROM products
                 WHERE stockquantity > 0"
            );
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching available products: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteOrder($order_id) {
        try {
            $this->db->query(
                "DELETE FROM transactions WHERE order_id = :order_id",
                ['order_id' => $order_id]
            );

            $this->db->query(
                "DELETE FROM orders WHERE id = :order_id",
                ['order_id' => $order_id]
            );

            return true;
        } catch (Exception $e) {
            error_log("Error deleting order: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateOrderStatus($order_id, $new_status) {
        try {
            $orderExists = $this->db->query(
                "SELECT id FROM orders WHERE id = :order_id",
                ['order_id' => $order_id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$orderExists) {
                throw new Exception("Order not found");
            }

            $validStatuses = ['Pending', 'Delivered', 'Canceled', 'Returned'];
            if (!in_array($new_status, $validStatuses)) {
                throw new Exception("Invalid order status");
            }

            $this->db->query(
                "UPDATE orders SET orderstatus = :orderstatus WHERE id = :order_id",
                [
                    'orderstatus' => $new_status,
                    'order_id' => $order_id
                ]
            );

            $this->db->query(
                "INSERT INTO notifications (user_id, message, notedate)
                 SELECT user_id, CONCAT('Order #', :order_id, ' status updated to ', :orderstatus), NOW()
                 FROM orders WHERE id = :order_id",
                [
                    'order_id' => $order_id,
                    'orderstatus' => $new_status
                ]
            );

            return true;
        } catch (Exception $e) {
            error_log("Error updating order status: " . $e->getMessage());
            throw $e;
        }
    }

    public function cancelOrder($order_id) {
        try {
            $order = $this->db->query(
                "SELECT orderstatus, user_id FROM orders WHERE id = :order_id",
                ['order_id' => $order_id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new Exception("Order not found");
            }

            if ($order['orderstatus'] === 'Canceled') {
                throw new Exception("Order is already canceled");
            }

            if (in_array($order['orderstatus'], ['Delivered', 'Returned'])) {
                throw new Exception("Cannot cancel a delivered or returned order");
            }

            $this->db->query(
                "UPDATE orders SET orderstatus = 'Canceled' WHERE id = :order_id",
                ['order_id' => $order_id]
            );

            $this->db->query(
                "UPDATE products p
                 JOIN orderitems oi ON p.id = oi.product_id
                 SET p.stockquantity = p.stockquantity + oi.quantity
                 WHERE oi.order_id = :order_id",
                ['order_id' => $order_id]
            );

            $this->db->query(
                "INSERT INTO inventory (product_id, stockIn, stockOut, updatedate)
                 SELECT oi.product_id, oi.quantity, 0, NOW()
                 FROM orderitems oi
                 WHERE oi.order_id = :order_id",
                ['order_id' => $order_id]
            );

            $this->db->query(
                "INSERT INTO notifications (user_id, message, notedate)
                 VALUES (:user_id, CONCAT('Order #', :order_id, ' has been canceled.'), NOW())",
                [
                    'user_id' => $order['user_id'],
                    'order_id' => $order_id
                ]
            );

            return true;
        } catch (Exception $e) {
            error_log("Error canceling order: " . $e->getMessage());
            throw $e;
        }
    }

    public function cancelExpiredPendingOrders() {
        try {
            $expiredOrders = $this->db->query(
                "SELECT id, user_id
                 FROM orders
                 WHERE orderstatus = 'Pending'
                 AND orderdate < NOW() - INTERVAL 24 HOUR"
            )->fetchAll(PDO::FETCH_ASSOC);

            $canceledCount = 0;

            foreach ($expiredOrders as $order) {
                $this->db->query(
                    "UPDATE orders SET orderstatus = 'Canceled' WHERE id = :order_id",
                    ['order_id' => $order['id']]
                );

                $this->db->query(
                    "UPDATE products p
                     JOIN orderitems oi ON p.id = oi.product_id
                     SET p.stockquantity = p.stockquantity + oi.quantity
                     WHERE oi.order_id = :order_id",
                    ['order_id' => $order['id']]
                );

                $this->db->query(
                    "INSERT INTO inventory (product_id, stockIn, stockOut, updatedate)
                     SELECT oi.product_id, oi.quantity, 0, NOW()
                     FROM orderitems oi
                     WHERE oi.order_id = :order_id",
                    ['order_id' => $order['id']]
                );

                $this->db->query(
                    "INSERT INTO notifications (user_id, message, notedate)
                     VALUES (:user_id, CONCAT('Order #', :order_id, ' was canceled due to expiration.'), NOW())",
                    [
                        'user_id' => $order['user_id'],
                        'order_id' => $order['id']
                    ]
                );

                $canceledCount++;
            }

            return $canceledCount;
        } catch (Exception $e) {
            error_log("Error canceling expired orders: " . $e->getMessage());
            throw $e;
        }
    }

    // get locations 
    public function getLocations() {
        $sql = "SELECT id, location_name FROM locations";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // update order
    public function getProductById($productId) {
        try {
            $result = $this->db->query(
                "SELECT id, productname, price, stockquantity FROM products WHERE id = :id",
                ['id' => $productId]
            );
            return $result->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching product by ID: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function updateProductStock($productId, $newStock) {
        try {
            $this->db->query(
                "UPDATE products SET stockquantity = :stockquantity WHERE id = :id",
                [
                    'stockquantity' => $newStock,
                    'id' => $productId
                ]
            );
            return true;
        } catch (Exception $e) {
            error_log("Error updating product stock: " . $e->getMessage());
            throw $e;
        }
    }

    // for chat
    public function saveMessage($orderId, $senderId, $receiverId, $message) {
        try {
            $params = [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message' => $message
            ];
            $sql = "INSERT INTO messages (order_id, sender_id, receiver_id, message, sent_at) 
                    VALUES (:order_id, :sender_id, :receiver_id, :message, NOW())";
            if ($orderId) {
                $params['order_id'] = $orderId;
            } else {
                $sql = str_replace(':order_id', 'NULL', $sql);
            }
            $this->db->query($sql, $params);
            return true;
        } catch (Exception $e) {
            error_log("Error saving message: " . $e->getMessage());
            throw $e;
        }
    }
    // get messages by
    public function getMessagesByOrder($orderId, $userId) {
        try {
            $sql = "SELECT m.*, u1.username AS sender_name, u2.username AS receiver_name
                    FROM messages m
                    JOIN users u1 ON m.sender_id = u1.id
                    JOIN users u2 ON m.receiver_id = u2.id
                    WHERE m.order_id = :order_id 
                    AND (m.sender_id = :user_id OR m.receiver_id = :user_id)
                    ORDER BY m.sent_at ASC";
            $params = [
                'order_id' => $orderId,
                'user_id' => $userId
            ];
            if (!$orderId) {
                $sql = str_replace('m.order_id = :order_id', 'm.order_id IS NULL', $sql);
                unset($params['order_id']);
            }
            $result = $this->db->query($sql, $params);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching messages: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUserById($userId) {
        try {
            $sql = "SELECT role FROM users WHERE id = :id";
            $params = ['id' => $userId];
            $result = $this->db->query($sql, $params);
            return $result->fetch() ?: null;
        } catch (Exception $e) {
            error_log("Error in getUserById: " . $e->getMessage());
            throw $e;
        }
    }

    //get all admin
    public function getAllAdmins() {
        try {
            $sql = "SELECT id FROM users WHERE role = 'admin' ORDER BY id";
            return $this->db->query($sql)->fetchAll();
        } catch (Exception $e) {
            error_log("Error in getAllAdmins: " . $e->getMessage());
            throw $e;
        }
    }
    

}
?>