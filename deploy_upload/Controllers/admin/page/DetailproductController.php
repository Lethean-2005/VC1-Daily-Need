<?php
    class DetailController extends BasecustomerController{
        public function index(){
            $productModel = new ProductModel();

            $productId = isset($_GET['productId']) ? (int) $_GET['productId'] : 0;
            $product = $productId ? $productModel->getProductById($productId) : null;

            if (!$product) {
                $all = $productModel->getProducts();
                $product = $all[0] ?? null;
            }

            $reviews = [];
            $reviewStats = ['avg_rating' => 0, 'review_count' => 0];
            $related = [];

            if ($product) {
                $reviews = $productModel->getReviewsForProduct($product['id']);
                $reviewStats = $productModel->getReviewStats($product['id']);
                $related = $productModel->getRelatedProducts($product['id'], $product['categories'], 5);
            }

            $this->view('pages/product_detail', [
                'product' => $product,
                'reviews' => $reviews,
                'reviewStats' => $reviewStats,
                'related' => $related,
            ]);
        }
    }
?>