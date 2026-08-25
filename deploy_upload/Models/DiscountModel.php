<?php
class DiscountModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }
}