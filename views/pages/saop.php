<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])): 
    require_once "Models/ProductModel.php";
    $productModel = new ProductModel();
    $dbProducts = $productModel->getProducts();
?>
    <style>
        body {
            background-color: #f5e6f5;
            min-height: 100vh;
            font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
            margin-top: 50px;
        }
        .shop {
            max-width: 1200px;
            margin: 0 auto;
        }
        .filter, .filter-categories {
            background-color: #fff;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .filter h3, .filter-categories h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        #priceRange {
            width: 100%;
        }
        #priceValue {
            display: block;
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            max-height: 160px;
            object-fit: contain;
        }
        .btn-purple {
            background-color: #6f42c1;
            border-radius: 15px;
            font-size: 12px;
            padding: 8px;
            width: 48%;
        }
        .btn-purple:hover {
            background-color: #5a2b96;
        }
        .btn-green {
            background-color: #28a745;
            border-radius: 15px;
            font-size: 12px;
            padding: 8px;
            width: 48%;
        }
        .btn-green:hover {
            background-color: #218838;
        }
        .title {
            color: #ffffff;
            text-align: center;
            background-color: rgb(12, 230, 242);
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 30px;
            width: 98%;
        }
        .no-results {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="shop container-fluid">
        <div class="row">
            <!-- Sidebar for Filters -->
            <div class="col-md-3">
                <div class="filter">
                    <h3>Filter by Price</h3>
                    <input type="range" id="priceRange" min="1" max="100" step="1" value="100" class="form-range">
                    <span id="priceValue">$1 - $100</span>
                    <form id="searchForm" method="GET" action="">
                        <input type="text" id="search" name="q" placeholder="Search by product name" class="form-control mt-3" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit" class="btn btn-primary mt-2 w-100">Search</button>
                    </form>
                </div>
            </div>

            <!-- Product Cards Section -->
            <div class="col-md-9">
                <div class="row px-3 py-4" id="productList">
                    <h2 class="title">Products</h2>
                    <?php
                    $search_query = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';
                    $filtered_products = [];

                    foreach ($dbProducts as $product) {
                        if (empty($search_query) || stripos($product['productname'], $search_query) !== false) {
                            $stock_status = $product['stockquantity'] <= 5 ? 'low' : 'high';
                            $filtered_products[] = [
                                'id' => $product['id'],
                                'img' => $product['imageURL'],
                                'title' => htmlspecialchars($product['productname']),
                                'description' => htmlspecialchars($product['description'] ?? 'No description available'),
                                'price' => $product['price'],
                                'stock' => $product['stockquantity'],
                                'stock_status' => $stock_status
                            ];
                        }
                    }

                    if (empty($filtered_products)) : ?>
                        <div class="no-results">
                            <p>No products found<?php echo $search_query ? " for search: '$search_query'" : ''; ?>.</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($filtered_products as $product) : ?>
                            <div class="col-md-4 col-sm-6 mb-4" data-product-id="<?php echo $product['id']; ?>">
                                <div class="card text-start shadow-sm d-flex flex-column" style="border-radius: 12px; overflow: hidden; height: 100%;">
                                    <div class="bg-light d-flex justify-content-center align-items-center" style="height: 180px;">
                                        <img src="<?php echo $product['img']; ?>" alt="<?php echo $product['title']; ?>" class="img-fluid">
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title"><?php echo $product['title']; ?></h6>
                                        <p class="card-text"><?php echo $product['description']; ?></p>
                                        <div class="price mt-auto">Price: $<?php echo number_format($product['price'], 2); ?></div>
                                        <div class="stock-info">
                                            <span class="<?php echo $product['stock_status'] === 'low' ? 'text-danger' : 'text-success'; ?>">
                                                Stock: <?php echo $product['stock']; ?>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button class="btn btn-purple text-white" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                                            <button class="btn btn-green text-white">Buy Now</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addToCart(id) {
            alert('Added product ID ' + id + ' to cart');
        }
    </script>

<?php endif; ?>
