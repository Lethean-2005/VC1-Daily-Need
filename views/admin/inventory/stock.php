<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) :
    require_once "Models/ProductModel.php";
    $productModel = new ProductModel();
    $dbProducts = $productModel->getProducts();
?>

    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(90deg, #f8e1e1 0%, #e1e1f8 100%);
            font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: 700;
            color: #000;
        }

        .search-bar {
            background: #f5f5f5;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            max-width: 400px;
            width: 100%;
        }

        .navbar .icons a {
            font-size: 20px;
            color: #333;
            position: relative;
        }

        .navbar .icons .badge {
            font-size: 10px;
            top: -8px;
            right: -8px;
        }

        /* Banner */
        .banner {
            background: url('views/assets/about-images/background.png') no-repeat center/cover;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }

        .banner-overlay {
            background: rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 15px;
        }

        .banner-content {
            text-align: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .banner-content h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .banner-content p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .banner-content .btn {
            background: #000;
            color: white;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: 500;
            text-transform: uppercase;
            transition: background 0.3s ease;
        }

        .banner-content .btn:hover {
            background: #333;
        }

        /* Sidebar */
        .category-list {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .category-card {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .category-card:hover {
            background: #f0f0f5;
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .category-card.active {
            background: #e0e0f0;
            font-weight: 600;
            transform: translateY(-2px);
        }

        .category-card .d-flex {
            align-items: center;
            gap: 10px;
        }

        .category-card i {
            font-size: 18px;
            color: #666;
        }

        /* Filters and Scan Product */
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
        }

        .scan-product {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .scan-product input {
            border: 1px solid #ddd;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            color: #666;
            background: #f9f9f9;
        }

        .scan-product .btn {
            background: #007bff;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .scan-product .btn:hover {
            background: #0056b3;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Fade-In Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid #e0e0e0;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .product-card img {
            height: 100px;
            width: 100%;
            object-fit: contain;
            padding: 10px;
            background: #f8f8f8;
            border-bottom: 1px solid #e0e0e0;
        }

        .card-body {
            padding: 10px;
            text-align: left;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .card-body h5 {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-body .creator {
            font-size: 11px;
            color: #777;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-body .price {
            font-size: 12px;
            font-weight: 500;
            color: #007bff;
            margin-bottom: 5px;
        }

        .card-body .stock-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .card-body .stock-info span {
            font-size: 11px;
            font-weight: 500;
        }

        .card-body .stock-info .stock-low {
            color: #dc3545;
        }

        .card-body .stock-info .stock-high {
            color: #28a745;
        }

        .card-body .stock-info .stock-icon {
            font-size: 12px;
        }

        /* Action Buttons */
        .card-actions {
            display: flex;
            gap: 8px;
            margin-top: 8px;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .card-actions form {
            margin: 0;
        }

        .card-actions button {
            display: flex;
            align-items: center;
            gap: 5px;
            background: none;
            border: 1px solid #e0e0e0;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.1s ease;
        }

        .card-actions button:hover {
            background: #f0f0f5;
        }

        .card-actions button:active {
            transform: scale(0.95);
        }

        .card-actions .edit-btn {
            color: #007bff;
        }

        .card-actions .edit-btn i {
            color: #007bff;
            font-size: 14px;
        }

        .card-actions .delete-btn {
            color: #dc3545;
        }

        .card-actions .delete-btn i {
            color: #dc3545;
            font-size: 14px;
        }

        .card-actions .add-btn {
            color: #28a745;
        }

        .card-actions .add-btn i {
            color: #28a745;
            font-size: 14px;
        }

        /* No Results Message */
        .no-results {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .no-results p {
            font-size: 16px;
            color: #666;
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .banner {
                height: 200px;
            }

            .banner-content h1 {
                font-size: 24px;
            }

            .banner-content p {
                font-size: 14px;
            }

            .product-card img {
                height: 80px;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }

            .filter-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .scan-product {
                width: 100%;
                margin-top: 10px;
            }

            .scan-product input {
                width: 100%;
            }

            .card-body h5 {
                font-size: 12px;
            }

            .card-body .creator {
                font-size: 10px;
            }

            .card-body .price {
                font-size: 11px;
            }

            .card-body .stock-info span {
                font-size: 10px;
            }

            .card-body .stock-info .stock-icon {
                font-size: 11px;
            }

            .card-actions {
                gap: 6px;
                margin-top: 6px;
            }

            .card-actions button {
                padding: 4px 8px;
                font-size: 11px;
            }

            .card-actions .edit-btn i,
            .card-actions .delete-btn i,
            .card-actions .add-btn i {
                font-size: 12px;
            }
        }
    </style>
    </head>

    <body>

        
        <!-- Banner -->
        <div class="banner">
            <div class="banner-overlay"></div>
            <div class="banner-content">
            <h1 style="color: white;">STOCKS</h1>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 ">
                <div class="category-list">
                    <?php
                    $categories = [
                        1 => 'Drinking Water',
                        2 => 'Beverages',
                        3 => 'Tissue',
                        4 => 'Snacks',
                        5 => 'Cooking Ingredients',
                        6 => 'House Hold Hygiene',
                        7 => 'Oral Health',
                        8 => 'Feminine Hygiene',
                        9 => 'Soap'
                    ];
                    $category_icons = [
                        1 => ['icon' => 'bi-droplet', 'color' => 'text-primary'],
                        2 => ['icon' => 'bi-cup-straw', 'color' => 'text-danger'], 
                        3 => ['icon' => 'bi-box', 'color' => 'text-success'], 
                        4 => ['icon' => 'bi-basket', 'color' => 'text-warning'],
                        5 => ['icon' => 'bi-egg', 'color' => 'text-info'], 
                        6 => ['icon' => 'bi-shield', 'color' => 'text-success'], 
                        7 => ['icon' => 'bi-basket', 'color' => 'text-warning'], 
                        8 => ['icon' => 'bi-heart', 'color' => 'text-danger'], 
                        9 => ['icon' => 'bi-droplet-fill', 'color' => 'text-primary'] 
                    ];
                    $selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 1; 
                    
                    foreach ($categories as $id => $name) : ?>
                        <form method="GET" action="" class="category-card d-block text-decoration-none text-dark <?php echo $selected_category === $id ? 'active' : ''; ?>">
                            <input type="hidden" name="category" value="<?php echo $id; ?>">
                            <button type="submit" style="border: none; background: none; width: 100%; text-align: left;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><?php echo $name; ?></span>
                                    
                                    <i class="bi <?php echo $category_icons[$id]['icon']; ?> <?php echo $category_icons[$id]['color']; ?>"></i>
                                </div>
                            </button>
                        </form>
                    <?php endforeach; ?>
                    
                </div>
            </div>

           <!-- Product Grid -->
<div class="col-md-9">
    <h4 class="mb-3"><?php echo $categories[$selected_category]; ?></h4>
    <div class="filter-section">
        <form method="GET" action="" class="scan-product">
            <input type="hidden" name="category" value="<?php echo $selected_category; ?>">
            <input type="text" name="barcode" placeholder="Scan Product Barcode..." value="<?php echo isset($_GET['barcode']) ? htmlspecialchars($_GET['barcode']) : ''; ?>">
        </form>
    </div>

    <div class="product-grid d-flex flex-wrap gap-4">
        <?php
        $category_map = array_flip($categories);

        $all_products = [];
        foreach ($dbProducts as $product) {
            $category_name = $product['categories'];
            $category_id = isset($category_map[$category_name]) ? $category_map[$category_name] : 0;
            if ($category_id === 0) continue;

            $stock_status = $product['stockquantity'] <= 5 ? 'low' : 'high';
            $all_products[$category_id][] = [
                'id' => $product['id'],
                'barcode' => $product['id'] . '000',
                'img' => $product['imageURL'],
                'alt' => $product['productname'],
                'title' => $product['productname'],
                'creator' => 'Creator',
                'price' => $product['price'],
                'stock' => $product['stockquantity'],
                'stock_status' => $stock_status
            ];
        }

        $products = $all_products[$selected_category] ?? [];

        $scanned_barcode = isset($_GET['barcode']) ? trim($_GET['barcode']) : '';
        $filtered_products = [];

        if (!empty($scanned_barcode)) {
            foreach ($products as $product) {
                if ($product['barcode'] === $scanned_barcode) {
                    $filtered_products[] = $product;
                    break;
                }
            }
            if (empty($filtered_products)) {
                foreach ($all_products as $category_products) {
                    foreach ($category_products as $product) {
                        if ($product['barcode'] === $scanned_barcode) {
                            $filtered_products[] = $product;
                            break 2;
                        }
                    }
                }
            }
        } else {
            $filtered_products = $products;
        }

        if (empty($filtered_products)) : ?>
            <div class="no-results">
                <p>No products found for barcode: <?php echo htmlspecialchars($scanned_barcode); ?></p>
            </div>
        <?php else : ?>
            <?php foreach ($filtered_products as $product) : ?>
                <div class="product-card bg-white border rounded shadow-sm p-3" style="width: 240px; position: relative;">
                    <!-- Product Image Container (Fixed Size) -->
                    <div style="width: 100%; height: 150px; overflow: hidden; border-radius: 0.5rem;">
                        <img src="<?php echo $product['img']; ?>"
                            alt="<?php echo $product['alt']; ?>"
                            style="width: 100%; height: 100%; object-fit: contain; background-color: #f8f9fa; padding: 5px;">
                    </div>
                    <div class="card-body p-0">
                        <h6 class="fw-bold mb-1"><?php echo $product['title']; ?></h6>

                        <!-- Creator & Three Dots in Same Row -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <p class="text-muted mb-0 small"><?php echo $product['creator']; ?></p>

                            <!-- Only Dropdown Icon (No Button) -->
                            <div class="dropdown">
                                <i class="ti ti-dots-vertical" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton<?php echo $product['id']; ?>">
                                    <!-- Edit -->
                                    <li>
                                        <form method="GET" action="/products/edit/<?php echo $product['id']; ?>" class="px-3 m-0">
                                            <button type="submit" class="btn btn-link text-start w-100">
                                                <i class="ti ti-pencil"></i> Edit
                                            </button>
                                        </form>
                                    </li>
                                    <!-- Delete -->
                                    <li>
                                        <form method="POST" action="/products/delete/<?php echo $product['id']; ?>" class="px-3 m-0">
                                            <button type="submit" class="btn btn-link text-start w-100 text-danger"
                                                onclick="return confirm('Are you sure you want to delete this product?');">
                                                <i class="ti ti-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                    <!-- Add Product -->
                                    <li>
                                        <a href="/add-product" class="dropdown-item text-success">
                                            <i class="ti ti-circle-plus"></i> Add Product
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Price -->
                        <p class="mb-1 text-primary fw-semibold">$<?php echo number_format($product['price'], 2); ?></p>

                        <!-- Stock Info -->
                        <div class="stock-info d-flex align-items-center">
                            <span class="<?php echo $product['stock_status'] === 'low' ? 'text-danger' : 'text-success'; ?> me-2">
                                Stock: <?php echo $product['stock']; ?>
                            </span>
                            <i class="bi bi-<?php echo $product['stock_status'] === 'low' ? 'exclamation-triangle-fill text-danger' : 'check-circle-fill text-success'; ?>"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

    </body>

    </html>
<?php
else:
    $this->redirect("/login");
endif;
?>