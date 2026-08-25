<?php
class StockModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Fetch stock history (both stockIn and stockOut) with product image (unchanged)
    public function getStockHistory($type = null) {
        $query = "
            SELECT 
                p.productname AS product,
                p.imageURL AS product_image,
                i.stockIn AS quantity_in,
                i.stockOut AS quantity_out,
                CASE 
                    WHEN i.stockIn > 0 AND i.stockOut = 0 THEN 'Stock In'
                    WHEN i.stockOut > 0 AND i.stockIn = 0 THEN 'Stock Out'
                    ELSE 'Mixed'
                END AS type,
                (p.price * (i.stockIn - i.stockOut)) AS total_price,
                i.updatedate AS date
            FROM inventory i
            JOIN products p ON i.product_id = p.id
        ";
        
        if ($type === 'in') {
            $query .= " WHERE i.stockIn > 0";
        } elseif ($type === 'out') {
            $query .= " WHERE i.stockOut > 0";
        }

        $query .= " ORDER BY i.updatedate DESC";
        
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch current stock levels from products table with product image
    public function getStockLevels() {
        $query = "
            SELECT 
                p.productname AS product,
                p.imageURL AS product_image,  -- NEW: Fetch product image
                p.stockquantity AS remaining_quantity
            FROM products p
            WHERE p.stockquantity >= 0
        ";
        
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update stockOut in inventory table (unchanged)
    public function incrementStockOut($product_id, $quantity) {
        try {
            $query = "
                INSERT INTO inventory (product_id, stockIn, stockOut, updatedate)
                VALUES (:product_id, 0, :quantity, NOW())
                ON DUPLICATE KEY UPDATE 
                    stockOut = stockOut + :quantity,
                    updatedate = NOW()
            ";
            $this->db->query($query, [
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
            error_log("StockOut incremented for product_id=$product_id by quantity=$quantity");
        } catch (Exception $e) {
            error_log("Error incrementing stockOut for product_id=$product_id: " . $e->getMessage());
            throw $e;
        }
    }
}
?>