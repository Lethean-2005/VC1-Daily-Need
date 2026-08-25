<?php
class CartModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::connect();
    }

    public function addItem($user_id, $product_id, $quantity = 1) {
        $existing = $this->getCartItem($user_id, $product_id);
        
        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            return $this->updateQuantity($user_id, $product_id, $newQuantity);
        } else {
            return $this->db->query(
                "INSERT INTO shoppingcarts (user_id, product_id, quantity) 
                VALUES (:user_id, :product_id, :quantity)",
                [
                    'user_id' => $user_id,
                    'product_id' => $product_id,
                    'quantity' => $quantity
                ]
            );
        }
    }

    private function getCartItem($user_id, $product_id) {
        $result = $this->db->query(
            "SELECT * FROM shoppingcarts 
            WHERE user_id = :user_id AND product_id = :product_id",
            [
                'user_id' => $user_id,
                'product_id' => $product_id
            ]
        );
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    private function updateQuantity($user_id, $product_id, $quantity) {
        $stmt = $this->db->query(
            "UPDATE shoppingcarts 
            SET quantity = :quantity 
            WHERE user_id = :user_id AND product_id = :product_id",
            [
                'user_id' => $user_id,
                'product_id' => $product_id,
                'quantity' => $quantity
            ]
        );
        return $stmt->rowCount() > 0;
    }

    public function getCartItems($user_id) {
        $result = $this->db->query(
            "SELECT sc.*, p.productname, p.price, p.imageURL, p.stockquantity, p.descriptions 
            FROM shoppingcarts sc
            JOIN products p ON sc.product_id = p.id
            WHERE sc.user_id = :user_id",
            ['user_id' => $user_id]
        );
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeItem($user_id, $product_id) {
        return $this->db->query(
            "DELETE FROM shoppingcarts 
            WHERE user_id = :user_id AND product_id = :product_id",
            [
                'user_id' => $user_id,
                'product_id' => $product_id
            ]
        );
    }

    public function updateItem($user_id, $product_id, $quantity) {
        return $this->updateQuantity($user_id, $product_id, $quantity);
    }

    public function clearCart($user_id) {
        try {
            $this->db->query(
                "DELETE FROM shoppingcarts WHERE user_id = :user_id",
                ['user_id' => $user_id]
            );
        } catch (Exception $e) {
            error_log("Error clearing cart: " . $e->getMessage());
            throw $e;
        }
    }
}