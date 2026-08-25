<?php
class OrderHistoryModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getOrdersByUserId($user_id) {
        try {
            $result = $this->db->query(
                "SELECT o.*, oi.product_id, oi.quantity, p.productname, p.price, p.imageURL, l.location_name, r.requested_product_id, r.return_reason, r.status AS return_status
                 FROM orders o
                 LEFT JOIN orderitems oi ON o.id = oi.order_id
                 LEFT JOIN products p ON oi.product_id = p.id
                 LEFT JOIN locations l ON o.location_id = l.id
                 LEFT JOIN returns r ON o.id = r.order_id
                 WHERE o.user_id = :user_id
                 ORDER BY o.orderdate DESC",
                ['user_id' => $user_id]
            );
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching user orders: " . $e->getMessage());
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

    public function submitChangeRequest($user_id, $order_id, $requested_product_id, $return_reason) {
        try {
            // Check if order exists and is eligible
            $order = $this->db->query(
                "SELECT orderstatus, orderdate, payment_method_id, location_id, payments
                 FROM orders
                 WHERE id = :order_id AND user_id = :user_id",
                ['order_id' => $order_id, 'user_id' => $user_id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new Exception("Order not found or does not belong to user");
            }

            if ($order['orderstatus'] !== 'Delivered') {
                throw new Exception("Only delivered orders can be exchanged");
            }

            // Check 3-day window
            $deliveryDate = new DateTime($order['orderdate']);
            $currentDate = new DateTime();
            $interval = $deliveryDate->diff($currentDate);
            if ($interval->days > 3) {
                throw new Exception("Change request must be made within 3 days of delivery");
            }

            // Check if a change request already exists
            $existingReturn = $this->db->query(
                "SELECT id FROM returns WHERE order_id = :order_id",
                ['order_id' => $order_id]
            )->fetch(PDO::FETCH_ASSOC);

            if ($existingReturn) {
                throw new Exception("A change request for this order already exists");
            }

            // Validate requested product
            $product = $this->db->query(
                "SELECT stockquantity, price FROM products WHERE id = :product_id",
                ['product_id' => $requested_product_id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$product || $product['stockquantity'] <= 0) {
                throw new Exception("Requested product is not available");
            }

            // Insert change request
            $this->db->query(
                "INSERT INTO returns (order_id, user_id, return_reason, return_type, requested_product_id, status, request_date)
                 VALUES (:order_id, :user_id, :return_reason, 'Exchange', :requested_product_id, 'Pending', NOW())",
                [
                    'order_id' => $order_id,
                    'user_id' => $user_id,
                    'return_reason' => $return_reason,
                    'requested_product_id' => $requested_product_id
                ]
            );

            // Update order status
            $this->db->query(
                "UPDATE orders SET orderstatus = 'Returned' WHERE id = :order_id",
                ['order_id' => $order_id]
            );

            // Notify user
            $this->db->query(
                "INSERT INTO notifications (user_id, message, notedate)
                 VALUES (:user_id, CONCAT('Your product change request for order #', :order_id, ' has been submitted.'), NOW())",
                ['user_id' => $user_id, 'order_id' => $order_id]
            );

            return true;
        } catch (Exception $e) {
            error_log("Error submitting change request: " . $e->getMessage());
            throw $e;
        }
    }

    public function processChangeRequest($order_id, $return_action, $requested_product_id) {
        try {
            // Fetch return request and order details
            $return = $this->db->query(
                "SELECT r.*, o.user_id, o.payment_method_id, o.location_id, o.payments
                 FROM returns r
                 JOIN orders o ON r.order_id = o.id
                 WHERE r.order_id = :order_id AND r.status = 'Pending'",
                ['order_id' => $order_id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$return) {
                throw new Exception("No pending change request found for this order");
            }

            $newStatus = $return_action === 'Approve' ? 'Approved' : 'Rejected';

            // Validate requested product
            $product = $this->db->query(
                "SELECT stockquantity, price FROM products WHERE id = :product_id",
                ['product_id' => $requested_product_id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$product || $product['stockquantity'] <= 0) {
                throw new Exception("Selected replacement product is not available");
            }

            // Update return status
            $this->db->query(
                "UPDATE returns
                 SET status = :status, requested_product_id = :requested_product_id, processed_date = NOW()
                 WHERE order_id = :order_id",
                [
                    'status' => $newStatus,
                    'requested_product_id' => $requested_product_id,
                    'order_id' => $order_id
                ]
            );

            if ($return_action === 'Approve') {
                // Restock original products
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

                // Create new order for replacement product
                $newOrderId = $this->db->query(
                    "INSERT INTO orders (user_id, payment_method_id, location_id, totalprice, orderstatus, payments, orderdate)
                     VALUES (:user_id, :payment_method_id, :location_id, :totalprice, 'Pending', :payments, NOW())",
                    [
                        'user_id' => $return['user_id'],
                        'payment_method_id' => $return['payment_method_id'],
                        'location_id' => $return['location_id'],
                        'totalprice' => $product['price'],
                        'payments' => $return['payments']
                    ]
                );

                $newOrderId = $this->db->lastInsertId();

                // Add replacement product to new order
                $this->db->query(
                    "INSERT INTO orderitems (order_id, product_id, quantity)
                     VALUES (:order_id, :product_id, 1)",
                    [
                        'order_id' => $newOrderId,
                        'product_id' => $requested_product_id
                    ]
                );

                // Reduce stock for replacement product
                $this->db->query(
                    "UPDATE products
                     SET stockquantity = stockquantity - 1
                     WHERE id = :product_id",
                    ['product_id' => $requested_product_id]
                );

                $this->db->query(
                    "INSERT INTO inventory (product_id, stockIn, stockOut, updatedate)
                     VALUES (:product_id, 0, 1, NOW())",
                    ['product_id' => $requested_product_id]
                );

                // Notify user
                $this->db->query(
                    "INSERT INTO notifications (user_id, message, notedate)
                     VALUES (:user_id, CONCAT('Your product change request for order #', :order_id, ' has been approved. A new order (#', :new_order_id, ') has been created.'), NOW())",
                    [
                        'user_id' => $return['user_id'],
                        'order_id' => $order_id,
                        'new_order_id' => $newOrderId
                    ]
                );
            } else {
                // Revert order status to Delivered if rejected
                $this->db->query(
                    "UPDATE orders SET orderstatus = 'Delivered' WHERE id = :order_id",
                    ['order_id' => $order_id]
                );

                // Notify user
                $this->db->query(
                    "INSERT INTO notifications (user_id, message, notedate)
                     VALUES (:user_id, CONCAT('Your product change request for order #', :order_id, ' was rejected.'), NOW())",
                    ['user_id' => $return['user_id'], 'order_id' => $order_id]
                );
            }

            return true;
        } catch (Exception $e) {
            error_log("Error processing change request: " . $e->getMessage());
            throw $e;
        }
    }
}
?>