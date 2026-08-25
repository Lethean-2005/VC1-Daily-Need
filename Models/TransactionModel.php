<?php
class TransactionModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function createTransaction($order_id, $payment_method, $amount) {
        try {
            $this->db->query(
                "INSERT INTO transactions (order_id, payment_method, amount, transaction_date) 
                VALUES (:order_id, :payment_method, :amount, NOW())",
                [
                    'order_id' => $order_id,
                    'payment_method' => $payment_method,
                    'amount' => $amount
                ]
            );
            $transaction_id = $this->db->lastInsertId();
            error_log("TransactionModel::createTransaction - Successfully created transaction, ID: $transaction_id");
            return $transaction_id;
        } catch (Exception $e) {
            error_log("Error creating transaction: " . $e->getMessage());
            throw $e;
        }
    }
}