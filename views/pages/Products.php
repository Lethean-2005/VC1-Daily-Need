<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: /F_login");
    exit();
}

require_once "Models/ProductModel.php";
$productModel = new ProductModel();

$allowedSorts = ['newest', 'toprated', 'popular'];
$sort = in_array($_GET['sort'] ?? '', $allowedSorts) ? $_GET['sort'] : 'newest';

$products = $productModel->getProductsWithStats($sort);
$categoryCounts = $productModel->getCategoryCounts();

$categories = [
    ['label' => 'Oral Health',        'db' => 'Oral Health',         'href' => '/oral'],
    ['label' => 'Feminine Hygiene',   'db' => 'Feminine Hygiene',    'href' => '/feminine'],
    ['label' => 'Household Hygiene',  'db' => 'House Hold Hygiene',  'href' => '/houeshold'],
    ['label' => 'Tissue',             'db' => 'Tissue',              'href' => '/tissue'],
    ['label' => 'Drinking Water',     'db' => 'Drinking Water',      'href' => '/drinking'],
    ['label' => 'Beverages',          'db' => 'Beverages',           'href' => '/beverage'],
    ['label' => 'Soap',               'db' => 'Soap',                'href' => '/saop'],
    ['label' => 'Cooking Ingredients','db' => 'Cooking Ingredients', 'href' => '/cooking'],
    ['label' => 'Snacks',             'db' => 'Snacks',              'href' => '/snacks'],
];

$applied_coupon = null;
if (isset($_SESSION['applied_coupon']['code'])) {
    $coupon = $productModel->validateCoupon($_SESSION['applied_coupon']['code'], $_SESSION['user_id']);
    if ($coupon) {
        $applied_coupon = $coupon;
    } else {
        unset($_SESSION['applied_coupon']);
    }
}

function getDiscountedPrice($price, $coupon) {
    if (!$coupon) return $price;
    if ($coupon['discount_type'] === 'percentage') {
        return $price * (1 - $coupon['discount_value'] / 100);
    }
    return max(0, $price - $coupon['discount_value']);
}

function dn_stars($rating) {
    $full = (int) round($rating);
    return str_repeat('★', $full) . str_repeat('☆', 5 - $full);
}

$maxPrice = 0;
foreach ($products as $p) {
    $maxPrice = max($maxPrice, (float) $p['price']);
}
$maxPrice = $maxPrice > 0 ? ceil($maxPrice) : 100;
?>

<style>
    .dn-shop { background: #f7f6f3; padding: 32px 0 60px; }
    .dn-shop .container { max-width: 1200px; }
    .dn-shop-crumb { font-size: .82rem; color: #999; margin-bottom: 4px; }
    .dn-shop-crumb a { color: #999; text-decoration: none; }
    .dn-shop-title { font-size: 1.7rem; font-weight: 700; color: #1f2a1f; margin-bottom: 24px; }

    .dn-panel {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,.05);
        margin-bottom: 20px;
    }
    .dn-panel h3 { font-size: 1rem; font-weight: 700; color: #1f2a1f; margin-bottom: 14px; }

    .dn-cat-list { list-style: none; margin: 0; padding: 0; }
    .dn-cat-list li { margin-bottom: 4px; }
    .dn-cat-list a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 4px;
        color: #555;
        text-decoration: none;
        font-size: .88rem;
        border-radius: 6px;
    }
    .dn-cat-list a:hover { background: #f4f3ef; color: #1f2a1f; }
    .dn-cat-list .count { color: #aaa; font-size: .78rem; }

    .dn-price-value { text-align: center; font-size: .85rem; color: #666; margin-top: 8px; }

    .dn-toolbar {
        background: #fff;
        border-radius: 14px;
        padding: 14px 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,.05);
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
        justify-content: space-between;
    }
    .dn-search-box { position: relative; flex: 1 1 220px; max-width: 320px; }
    .dn-search-box input {
        width: 100%;
        padding: 9px 14px 9px 36px;
        border-radius: 8px;
        border: 1px solid #eee;
        background: #f7f6f3;
        font-size: .88rem;
    }
    .dn-search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999; }
    .dn-results-count { font-size: .82rem; color: #999; white-space: nowrap; }
    .dn-sort-tabs { display: flex; gap: 4px; background: #f7f6f3; border-radius: 8px; padding: 4px; }
    .dn-sort-tabs a {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: .82rem;
        font-weight: 600;
        color: #666;
        text-decoration: none;
    }
    .dn-sort-tabs a.active { background: #fff; color: #1f2a1f; box-shadow: 0 2px 6px rgba(0,0,0,.08); }

    .dn-product-card {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 14px rgba(0,0,0,.05);
        transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
    }
    .dn-product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 26px rgba(0,0,0,.1); }
    .dn-product-img {
        position: relative;
        background: #f7f6f3;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .dn-product-img img { max-height: 75%; max-width: 75%; object-fit: contain; }
    .dn-product-fav {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,.1);
    }
    .dn-product-fav.active { color: #d64545; }
    .dn-product-body { padding: 14px 16px; display: flex; flex-direction: column; flex: 1; }
    .dn-product-name { font-weight: 600; font-size: .92rem; margin-bottom: 6px; cursor: pointer; }
    .dn-product-price-row { margin-bottom: 6px; }
    .dn-product-price { font-weight: 700; color: #1f2a1f; }
    .dn-product-was { color: #aaa; text-decoration: line-through; font-size: .8rem; margin-left: 6px; }
    .dn-product-rating { font-size: .78rem; color: #f4b400; margin-bottom: 12px; }
    .dn-product-rating .count { color: #999; margin-left: 4px; }
    .dn-add-cart-btn {
        margin-top: auto;
        border: none;
        background: #1f2a1f;
        color: #fff;
        width: 100%;
        padding: 9px 0;
        border-radius: 8px;
        font-weight: 600;
        font-size: .85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .dn-add-cart-btn:hover { background: #d98c4a; }
</style>

<div class="dn-shop">
    <div class="container">
        <div class="dn-shop-crumb"><a href="/">Home</a> / Shop</div>
        <h1 class="dn-shop-title">Products</h1>

        <div class="row">
            <div class="col-lg-3">
                <div class="dn-panel">
                    <h3>Product Categories</h3>
                    <ul class="dn-cat-list">
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= htmlspecialchars($cat['href']) ?>">
                                    <span>&rsaquo; <?= htmlspecialchars($cat['label']) ?></span>
                                    <span class="count"><?= (int) ($categoryCounts[$cat['db']] ?? 0) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="dn-panel">
                    <h3>Price Range</h3>
                    <input type="range" id="priceRange" min="0" max="<?= (int) $maxPrice ?>" step="1" value="<?= (int) $maxPrice ?>" class="form-range">
                    <div class="dn-price-value">Up to $<span id="priceValue"><?= (int) $maxPrice ?></span></div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="dn-toolbar">
                    <div class="dn-search-box">
                        <i class="ti ti-search"></i>
                        <input type="text" id="search" placeholder="Search products...">
                    </div>
                    <div class="dn-results-count">Showing <?= count($products) ?> of <?= count($products) ?> results</div>
                    <div class="dn-sort-tabs">
                        <a href="?sort=toprated" class="<?= $sort === 'toprated' ? 'active' : '' ?>">Top Rated</a>
                        <a href="?sort=popular" class="<?= $sort === 'popular' ? 'active' : '' ?>">Popular</a>
                        <a href="?sort=newest" class="<?= $sort === 'newest' ? 'active' : '' ?>">Newest</a>
                    </div>
                </div>

                <div class="row g-4" id="productList">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product):
                            $original_price = (float) $product['price'];
                            $discounted_price = getDiscountedPrice($original_price, $applied_coupon);
                            $hasDiscount = $applied_coupon && $discounted_price < $original_price;
                        ?>
                            <div class="col-md-6 col-lg-4" data-product-id="<?= (int) $product['id'] ?>" data-price="<?= htmlspecialchars($discounted_price) ?>">
                                <div class="dn-product-card">
                                    <div class="dn-product-img">
                                        <i class="ti ti-heart dn-product-fav" data-heart-id="<?= (int) $product['id'] ?>" onclick="toggleFavorite(<?= (int) $product['id'] ?>)"></i>
                                        <img src="<?= htmlspecialchars($product['imageURL']) ?>" alt="<?= htmlspecialchars($product['productname']) ?>">
                                    </div>
                                    <div class="dn-product-body">
                                        <div class="dn-product-name" onclick="viewDetails(<?= (int) $product['id'] ?>)"><?= htmlspecialchars($product['productname']) ?></div>
                                        <div class="dn-product-price-row">
                                            <span class="dn-product-price">$<?= number_format($hasDiscount ? $discounted_price : $original_price, 2) ?></span>
                                            <?php if ($hasDiscount): ?>
                                                <span class="dn-product-was">$<?= number_format($original_price, 2) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="dn-product-rating">
                                            <?= dn_stars($product['avg_rating']) ?>
                                            <span class="count">(<?= (int) $product['review_count'] ?>)</span>
                                        </div>
                                        <button class="dn-add-cart-btn" onclick="addToCart(<?= (int) $product['id'] ?>)">
                                            <i class="ti ti-shopping-cart"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center text-muted">No products available.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('priceRange').addEventListener('input', function () {
    document.getElementById('priceValue').textContent = this.value;
    const maxPrice = parseFloat(this.value);
    document.querySelectorAll('#productList [data-product-id]').forEach(card => {
        const price = parseFloat(card.dataset.price);
        card.style.display = price <= maxPrice ? '' : 'none';
    });
});

document.getElementById('search').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    document.querySelectorAll('#productList [data-product-id]').forEach(card => {
        const name = card.querySelector('.dn-product-name').textContent.toLowerCase();
        card.style.display = name.includes(query) ? '' : 'none';
    });
});
</script>
