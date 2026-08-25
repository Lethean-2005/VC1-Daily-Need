<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

// Check if product data is available
if (!isset($data['product'])) {
    die("Product details not found.");
}

$product = $data['product'];
$imagePath = (!empty($product['imageURL']) && file_exists($product['imageURL'])) 
    ? $product['imageURL'] 
    : '/assets/images/placeholder.png'; // Default image
?>


    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
            background-color: #fff;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            width: 100%;
            max-width: 450px;
            height: 350px;
            object-fit: contain;
            border-radius: 10px;
            background-color: #fff;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        .product-image:hover {
            transform: scale(1.05);
        }
        .details-container {
            padding: 20px;
        }
        .back-btn {
            margin-top: 30px;
            text-align: center;
        }
        h2 {
            font-weight: 700;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        h3 {
            color: #34495e;
            font-weight: 600;
        }
        .text-muted {
            font-size: 1.1rem;
            color: #7f8c8d;
        }
        .price {
            font-size: 1.6rem;
            font-weight: bold;
            color: #27ae60;
            background: linear-gradient(90deg, #27ae60, #2ecc71);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stock-low {
            color: #e74c3c;
            font-weight: bold;
        }
        .btn-back {
            background-color: #3498db;
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-back:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
    </style>

    <div class="container my-5">
        <h2 class="text-center mb-5">🛍️ Product Details</h2>
        <div class="card">
            <div class="row g-4">
                <!-- Image Section -->
                <div class="col-lg-6 text-center">
                    <img src="/<?= htmlspecialchars($imagePath) ?>" 
                         alt="<?= htmlspecialchars($product['productname']) ?>" 
                         class="product-image">
                </div>
                <!-- Product Details Section -->
                <div class="col-lg-6 details-container">
                    <h3 class="mb-4"><?= htmlspecialchars($product['productname']) ?></h3>
                    <p class="text-muted"><strong>Category:</strong> <?= htmlspecialchars($product['categories']) ?></p>
                    <p class="text-muted"><strong>Price:</strong> <span class="price">$<?= htmlspecialchars($product['price']) ?></span></p>
                    <p class="text-muted"><strong>Stock:</strong> 
                        <span class="<?= $product['stockquantity'] <= 10 ? 'stock-low' : '' ?>">
                            <?= htmlspecialchars($product['stockquantity']) ?>
                        </span>
                    </p>
                    <p class="text-muted"><strong>Description:</strong> <?= htmlspecialchars($product['descriptions']) ?></p>
                </div>
            </div>
        </div>
        <div class="back-btn">
            <a href="/products" class="btn btn-back text-white">Back to Products</a>
        </div>
    </div>

