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
        #searchForm .form-control {
            border-radius: 20px;
            border: 1px solid #ddd;
        }
        #searchForm .btn-primary {
            background-color: #6f42c1;
            border: none;
            border-radius: 20px;
            padding: 10px;
        }
        #searchForm .btn-primary:hover {
            background-color: #5a2b96;
        }
        .filter-categories .list-group-item {
            border: none;
            padding: 10px 0;
            font-size: 14px;
        }
        .filter-categories .list-group-item a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
        }
        .filter-categories .list-group-item a:hover {
            color: #6f42c1;
        }
        .filter-categories .list-group-item i {
            margin-right: 10px;
            color: #6f42c1;
            font-size: 16px;
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
        .card-body {
            padding: 15px;
        }
        .card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .card-text {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        .rating {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .rating .star {
            font-size: 14px;
            cursor: pointer;
            color: #dddd;
            transition: color 0.2s ease;
        }
        .rating .star.filled {
            color: rgb(255, 217, 0);
        }
        .rating .rating-value {
            font-size: 12px;
            color: #666;
            margin-left: 5px;
        }
        .price {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        .stock-info {
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        .stock-low {
            color: #dc3545;
        }
        .stock-high {
            color: #28a745;
        }
        .btn-purple, .btn-green {
            border-radius: 15px;
            font-size: 12px;
            padding: 8px;
            width: 48%;
            transition: background-color 0.3s ease;
        }
        .btn-purple {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }
        .btn-purple:hover {
            background-color: #5a2b96;
            border-color: #5a2b96;
        }
        .btn-green {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-green:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .bi-heart, .bi-heart-fill {
            font-size: 16px;
            cursor: pointer;
        }
        .bi-heart-fill {
            color: #6f42c1;
        }
        .view-details-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .card:hover .view-details-btn {
            opacity: 1;
        }
        @media (max-width: 767px) {
            .filter, .filter-categories {
                margin: 0 15px 20px;
            }
            .card {
                margin: 0 auto;
                max-width: 300px;
            }
        }
        .title {
            color: #ffffff;
            font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
            text-align: center;
            background-color: rgb(12, 230, 242);
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 10px;
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
                <div class="filter-categories mt-4">
                    <h3>Filter by Categories</h3>
                    <ul class="list-group">
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
                            1 => 'fas fa-tint',
                            2 => 'fas fa-coffee',
                            3 => 'fas fa-toilet-paper',
                            4 => 'fas fa-cookie',
                            5 => 'fas fa-utensils',
                            6 => 'fas fa-home',
                            7 => 'fas fa-tooth',
                            8 => 'fas fa-female',
                            9 => 'fas fa-soap'
                        ];
                        $category_colors = [
                            1 => 'rgb(109, 200, 239)',
                            2 => 'rgb(209, 179, 10)',
                            3 => 'rgb(16, 198, 168)',
                            4 => 'rgb(121, 201, 22)',
                            5 => 'rgb(12, 211, 188)',
                            6 => 'rgb(41, 8, 161)',
                            7 => 'rgb(12, 230, 242)',
                            8 => 'rgb(155, 24, 215)',
                            9 => 'rgb(210, 17, 129)'
                        ];
                        $selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 1; // Default to Drinking Water (1)

                        foreach ($categories as $id => $name) : ?>
                            <li class="list-group-item">
                                <a href="?category=<?php echo $id; ?>" class="d-flex align-items-center">
                                    <i class="<?php echo $category_icons[$id]; ?> me-3" style="color: <?php echo $category_colors[$id]; ?>;"></i>
                                    <span><?php echo $name; ?> (<?php echo count(array_filter($dbProducts, fn($p) => $p['categories'] === $name)); ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <!-- Product Cards Section -->
            <div class="col-md-9">
                <div class="row px-3 py-4" id="productList">
                    <h2 class="title"><?php echo $categories[$selected_category]; ?></h2>
                    <?php
                    $filtered_products = [];
                    $search_query = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';

                    foreach ($dbProducts as $product) {
                        $category_name = $product['categories'];
                        $category_id = isset(array_flip($categories)[$category_name]) ? array_flip($categories)[$category_name] : 0;

                        // Filter by selected category
                        if ($category_id === $selected_category) {
                            // Further filter by search query if provided
                            if (empty($search_query) || stripos(strtolower($product['productname']), $search_query) !== false) {
                                $stock_status = $product['stockquantity'] <= 5 ? 'low' : 'high';
                                $filtered_products[] = [
                                    'id' => $product['id'],
                                    'img' => $product['imageURL'],
                                    'title' => $product['productname'],
                                    'description' => $product['description'] ?? 'No description available',
                                    'price' => $product['price'],
                                    'stock' => $product['stockquantity'],
                                    'stock_status' => $stock_status
                                ];
                            }
                        }
                    }

                    if (empty($filtered_products)) : ?>
                        <div class="no-results">
                            <p>No products found<?php echo $search_query ? " for search: '$search_query'" : ''; ?> in <?php echo $categories[$selected_category]; ?>.</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($filtered_products as $product) : ?>
                            <div class="col-md-4 col-sm-6 mb-4" data-product-id="<?php echo $product['id']; ?>">
                                <div class="card text-start shadow-sm d-flex flex-column" style="border-radius: 12px; overflow: hidden; height: 100%;">
                                    <div class="bg-light d-flex justify-content-center align-items-center" style="height: 180px; border-top-left-radius: 12px; border-top-right-radius: 12px; position: relative;">
                                        <img src="<?php echo $product['img']; ?>" alt="<?php echo $product['title']; ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                        <button class="view-details-btn" onclick="viewDetails(<?php echo $product['id']; ?>)">View Details</button>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="card-title"><?php echo $product['title']; ?></h6>
                                            <i class="ti ti-heart" data-heart-id="<?php echo $product['id']; ?>" onclick="toggleFavorite(<?php echo $product['id']; ?>)"></i>
                                        </div>
                                        <div class="rating mb-2">
                                            <span class="star" data-star="1" onclick="setRating(<?php echo $product['id']; ?>, 1)">★</span>
                                            <span class="star" data-star="2" onclick="setRating(<?php echo $product['id']; ?>, 2)">★</span>
                                            <span class="star" data-star="3" onclick="setRating(<?php echo $product['id']; ?>, 3)">★</span>
                                            <span class="star" data-star="4" onclick="setRating(<?php echo $product['id']; ?>, 4)">★</span>
                                            <span class="star" data-star="5" onclick="setRating(<?php echo $product['id']; ?>, 5)">★</span>
                                            <span class="rating-value" data-rating-id="<?php echo $product['id']; ?>">(0)</span>
                                        </div>
                                        <p class="card-text"><?php echo $product['description']; ?></p>
                                        <div class="price mt-auto">Price: $<?php echo number_format($product['price'], 2); ?></div>
                                        <div class="stock-info">
                                            <span class="<?php echo $product['stock_status'] === 'low' ? 'stock-low' : 'stock-high'; ?>">
                                                Stock: <?php echo $product['stock']; ?>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button class="btn btn-purple text-white" onclick="addToCart(<?php echo $product['id']; ?>)"><i class="ti ti-shopping-cart"></i> Add to Cart</button>
                                            <button class="btn btn-green text-white"><i class="ti ti-circle-check"></i> Buy Now</button>
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

    <!-- JavaScript for basic interactivity (optional) -->
    <script>
        function viewDetails(id) {
            alert('View details for product ID: ' + id);
        }
        function toggleFavorite(id) {
            const heart = document.querySelector(`[data-heart-id="${id}"]`);
            heart.classList.toggle('bi-heart');
            heart.classList.toggle('bi-heart-fill');
        }
        function setRating(id, rating) {
            alert('Set rating ' + rating + ' for product ID: ' + id);
        }
        function addToCart(id) {
            alert('Added product ID ' + id + ' to cart');
        }
    </script>


<?php else: ?>
    <?php $this->redirect("/login"); ?>
<?php endif; ?>