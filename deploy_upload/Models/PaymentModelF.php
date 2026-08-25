<?php
class PaymentModelF {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function savePaymentMethod($user_id, $card_number, $card_holder_name, $expiry_date, $cvv, $currency, $payment_method_type = null) {
        try {
            error_log("PaymentModelF::savePaymentMethod - Raw card_number: $card_number, payment_method_type: $payment_method_type");

            $card_number = preg_replace('/\s+/', '', $card_number);

            error_log("PaymentModelF::savePaymentMethod - Card_number after removing spaces: $card_number");

            if ($payment_method_type !== 'Paypal' && !preg_match('/^\d{16}$/', $card_number)) {
                throw new Exception("Card number must be exactly 16 digits (only numbers allowed). Got: $card_number");
            }

            if ($payment_method_type === 'Paypal') {
                $card_number = null;
                $card_holder_name = null;
                $expiry_date = null;
                $cvv = null;
            }

            $existing = $this->db->query(
                "SELECT id FROM payment_methods WHERE user_id = :user_id AND (card_number = :card_number OR (card_number IS NULL AND :card_number IS NULL))",
                [
                    'user_id' => $user_id,
                    'card_number' => $card_number
                ]
            )->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                error_log("PaymentModelF::savePaymentMethod - Payment method already exists for user_id: $user_id, returning existing ID: " . $existing['id']);
                return $existing['id'];
            }

            $result = $this->db->query(
                "INSERT INTO payment_methods (user_id, card_number, card_holder_name, expiry_date, cvv, currency) 
                VALUES (:user_id, :card_number, :card_holder_name, :expiry_date, :cvv, :currency)",
                [
                    'user_id' => $user_id,
                    'card_number' => $card_number,
                    'card_holder_name' => $card_holder_name,
                    'expiry_date' => $expiry_date,
                    'cvv' => $cvv,
                    'currency' => $currency
                ]
            );
            $new_id = $this->db->lastInsertId();
            error_log("PaymentModelF::savePaymentMethod - Successfully saved payment method, new ID: $new_id, type: $payment_method_type");
            return $new_id;
        } catch (Exception $e) {
            error_log("Error saving payment method: " . $e->getMessage());
            throw $e;
        }
    }

    public function getPaymentMethodById($payment_method_id) {
        try {
            $result = $this->db->query(
                "SELECT * FROM payment_methods WHERE id = :id",
                ['id' => $payment_method_id]
            );
            $payment_method = $result->fetch(PDO::FETCH_ASSOC);
            error_log("PaymentModelF::getPaymentMethodById - Fetched payment method ID: $payment_method_id, card_number: " . ($payment_method['card_number'] ?? 'null'));
            return $payment_method;
        } catch (Exception $e) {
            error_log("Error fetching payment method: " . $e->getMessage());
            throw $e;
        }
    }
}