<?php
class ProductModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::connect();
    }
    
    public function getProducts() {
        $result = $this->db->query("SELECT * FROM products");
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProductById($id) {
        $result = $this->db->query("SELECT * FROM products WHERE id = :id", ['id' => $id]);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Products with real aggregated review/sales stats, for the shop listing page.
    public function getProductsWithStats($sort = 'newest') {
        $orderBy = 'p.id DESC';
        if ($sort === 'toprated') {
            $orderBy = 'avg_rating DESC, review_count DESC';
        } elseif ($sort === 'popular') {
            $orderBy = 'units_sold DESC';
        }

        $result = $this->db->query(
            "SELECT p.*,
                    COALESCE(AVG(r.rating), 0) AS avg_rating,
                    COUNT(DISTINCT r.id) AS review_count,
                    COALESCE(SUM(oi.quantity), 0) AS units_sold
             FROM products p
             LEFT JOIN reviews r ON r.product_id = p.id
             LEFT JOIN orderitems oi ON oi.product_id = p.id
             GROUP BY p.id
             ORDER BY $orderBy"
        );
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Reviews for a single product, with the reviewer's username.
    public function getReviewsForProduct($productId) {
        $result = $this->db->query(
            "SELECT r.*, u.username
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.product_id = :id
             ORDER BY r.reviewdate DESC",
            ['id' => $productId]
        );
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Average rating + review count for a single product.
    public function getReviewStats($productId) {
        $result = $this->db->query(
            "SELECT COALESCE(AVG(rating), 0) AS avg_rating, COUNT(*) AS review_count
             FROM reviews WHERE product_id = :id",
            ['id' => $productId]
        );
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Other products in the same category, for the "more like this" strip.
    public function getRelatedProducts($productId, $category, $limit = 5) {
        $limit = (int) $limit;
        $result = $this->db->query(
            "SELECT * FROM products WHERE categories = :cat AND id != :id ORDER BY id LIMIT $limit",
            ['cat' => $category, 'id' => $productId]
        );
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Real per-category product counts, for the sidebar filter.
    public function getCategoryCounts() {
        $result = $this->db->query(
            "SELECT categories, COUNT(*) AS total FROM products GROUP BY categories"
        );
        $counts = [];
        foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $counts[$row['categories']] = (int) $row['total'];
        }
        return $counts;
    }

    public function addProduct($productName, $description, $category, $price, $stockQuantity, $imagePath) {
        $result = $this->db->query(
            "INSERT INTO products (productname, descriptions, categories, price, stockquantity, imageURL) 
            VALUES (:productname, :descriptions, :categories, :price, :stockquantity, :imageURL)", 
            [
                'productname' => $productName, 
                'descriptions' => $description, 
                'categories' => $category, 
                'price' => $price, 
                'stockquantity' => $stockQuantity, 
                'imageURL' => $imagePath
            ]
        );
        
        $productId = $this->db->lastInsertId();
        if ($stockQuantity > 0) {
            $this->addStock($productId, $stockQuantity, 0, date('Y-m-d H:i:s'));
            error_log("Added stockIn for product_id=$productId with quantity=$stockQuantity");
        }
        
        return $result;
    }

    public function addStock($productId, $stockIn, $stockOut, $updateDate) {
        $result = $this->db->query(
            "INSERT INTO inventory (product_id, stockIn, stockOut, updatedate) 
            VALUES (:product_id, :stockIn, :stockOut, :updatedate)", 
            [
                'product_id' => $productId, 
                'stockIn' => $stockIn, 
                'stockOut' => $stockOut, 
                'updatedate' => $updateDate
            ]
        );
        return $result;
    }

    public function deleteStock($productId, $stockIn, $stockOut, $updateDate) {
        $result = $this->db->query(
            "INSERT INTO inventory (product_id, stockIn, stockOut, updatedate) 
            VALUES (:product_id, :stockIn, :stockOut, :updatedate)", 
            [
                'product_id' => $productId, 
                'stockIn' => $stockIn, 
                'stockOut' => $stockOut, 
                'updatedate' => $updateDate
            ]
        );
        return $result;
    }

    public function updateProduct($productName, $description, $category, $price, $stockQuantity, $imagePath, $id) {
        $result = $this->db->query(
            "UPDATE products SET productname = :productname, descriptions = :descriptions, categories = :categories, price = :price, stockquantity = :stockquantity, imageURL = :imageURL WHERE id = :id", 
            [
                'productname' => $productName, 
                'descriptions' => $description, 
                'categories' => $category, 
                'price' => $price, 
                'stockquantity' => $stockQuantity, 
                'imageURL' => $imagePath,
                'id' => $id
            ]
        );

        $existingProduct = $this->getProductById($id);
        $oldStockQuantity = $existingProduct['stockquantity'];
        if ($stockQuantity > $oldStockQuantity) {
            $additionalStock = $stockQuantity - $oldStockQuantity;
            $this->addStock($id, $additionalStock, 0, date('Y-m-d H:i:s'));
            error_log("Updated stockIn for product_id=$id with additional quantity=$additionalStock");
        }

        return $result;
    }

    public function deleteProduct($id) {
        $result = $this->db->query("DELETE FROM products WHERE id = :id", ['id' => $id]);
        return $result;
    }

    public function checkDiscountCodeExists($code) {
        $result = $this->db->query("SELECT COUNT(*) FROM promo_codes WHERE code = :code", ['code' => $code]);
        return $result->fetchColumn() > 0;
    }

    public function addDiscountCode($code, $discount_type, $discount_value, $max_usage, $expiry_date) {
        if ($this->checkDiscountCodeExists($code)) {
            return false;
        }
        
        $this->db->query(
            "INSERT INTO promo_codes (code, discount_type, discount_value, max_usage, expiry_date) 
             VALUES (:code, :discount_type, :discount_value, :max_usage, :expiry_date)",
            [
                'code' => $code,
                'discount_type' => $discount_type,
                'discount_value' => $discount_value,
                'max_usage' => $max_usage,
                'expiry_date' => $expiry_date
            ]
        );
        return true;
    }

    public function validateCoupon($code, $user_id) {
        $result = $this->db->query(
            "SELECT id, discount_type, discount_value 
             FROM promo_codes 
             WHERE code = :code 
             AND is_active = 1
             AND (max_usage IS NULL OR usage_count < max_usage)
             AND (expiry_date IS NULL OR expiry_date > NOW())",
            ['code' => $code]
        );
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementCouponUsage($promo_code_id, $user_id) {
        $this->db->query(
            "UPDATE promo_codes SET usage_count = usage_count + 1 WHERE id = :id",
            ['id' => $promo_code_id]
        );
    }

    public function getCouponExpiry($promo_code_id) {
        $result = $this->db->query(
            "SELECT expiry_date FROM promo_codes WHERE id = :id",
            ['id' => $promo_code_id]
        );
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['expiry_date'] ?? null;
    }
}
?>