<style>
    /* Global Styles */
    body,html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
        color: #333;
        line-height: 1.6;
        background-color: #f4f3ef;
        background-image: radial-gradient(rgba(0,0,0,.08) 1px, transparent 1px);
        background-size: 16px 16px;
    }

    h1,h2,h3,h4,h5,h6 {
        font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
        font-weight: 700;
    }

    /* Profile Section Styling */
    .profile {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background-color: rgb(233, 166, 226);
        padding: 0 20px;
    }

    .profile .container {
        max-width: 1200px;
        width: 100%;
    }

    .profile-img-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
    }

    .profile-img {
        transition: transform 0.3s ease, opacity 0.3s ease;
        border-radius: 15px;
        width: 50%;
        height: auto;
    }

    .profile-img:hover {
        transform: scale(1.05);
    }

    .text-success {
        color: orange !important;
    }

    .btn-success {
        background-color: rgb(191, 73, 223);
        transition: background-color 0.3s ease, transform 0.3s ease;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .btn-success:hover {
        background-color: rgb(94, 10, 81);
        transform: translateY(-2px);
    }

    .lead {
        font-size: 1.25rem;
        line-height: 1.8;
        font-weight: 400;
        color: #555;
    }

    .text-muted {
        color: black !important;
    }


    /* Card Icons */
    .icon {
        font-size: 40px;
        margin-bottom: 15px;
        color: #007bff;
    }

    .icon-large {
        font-size: 2.5rem;
    }

    /* Card Text */
    .card-title {
        font-size: 20px;
        font-weight: bold;
    }

    /* Stats Section */
    .stats h3 {
        font-size: 2rem;
    }

    /* Base styles for cards */
    .small-card {
        border: 2px solid #ccc;
        position: relative;
        overflow: hidden;
        background-color: #fff;
        color: #333;
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, color 0.3s ease;
    }

    /* Hover State: Color changes and lifts */
    .hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: rgb(27, 155, 96);
        background-color: #fff;
        color: #3cdb6e;
    }

    /* Responsive Adjustments */
    .transition-card {
        transition: transform 0.3s ease, background 0.3s ease;
    }

    .transition-card:hover {
        transform: translateY(-10px);
        background: #f8f9fa;
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

    .btn-purple,
    .btn-green {
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

    .bi-heart,
    .bi-heart-fill {
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


    .col-md-9 {
        text-align: center;
        margin-top: 0;
        margin-left: 30vh;
    }

@media (max-width: 480px) {
    #productList {
        grid-template-columns: 1fr;
    }
    
    .profile-img {
        width: min(100%, 280px);
    }
    
    .btn-success {
        width: 100%;
        max-width: 280px;
    }
    
    /* Better spacing for mobile */
    .profile {
        padding: 2rem 0.5rem;
    }
    
    .card {
        margin-bottom: 1rem;
    }
       .col-md-9 {
        margin: 0;
        padding: 0 0.75rem;
        width: 100%;
    }

    
    /* Card grid - modern grid implementation */
    #productList {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
        padding: 0;
    }
}
</style>

<!-- About Hero -->
<style>
.dn-about-hero { background: #eef6f5; padding: 70px 0 0; overflow: hidden; }
.dn-about-hero .container { max-width: 1200px; }
.dn-about-hero-text h1 { font-size: 2.6rem; font-weight: 800; color: #0F5553; margin-bottom: 6px; }
.dn-about-hero-text h2 { font-size: 1.7rem; font-weight: 800; color: #14110d; line-height: 1.3; margin-bottom: 16px; }
.dn-about-hero-text p { color: #666; font-size: 1rem; max-width: 420px; margin-bottom: 28px; }
.dn-about-hero-actions { display: flex; align-items: center; gap: 14px; margin-bottom: 40px; }
.dn-about-cta {
    display: inline-flex;
    align-items: center;
    background: #0F5553;
    color: #fff;
    font-weight: 700;
    font-size: .82rem;
    letter-spacing: .04em;
    text-transform: uppercase;
    padding: 15px 26px;
    border-radius: 5px;
    text-decoration: none;
    transition: background .2s ease-in-out;
}
.dn-about-cta:hover { background: #0c433f; color: #fff; }
.dn-about-play {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #fff;
    color: #0F5553;
    font-size: 1rem;
    box-shadow: 0 6px 16px rgba(0,0,0,.08);
    text-decoration: none;
    flex-shrink: 0;
}
.dn-about-hero-img { width: 100%; max-width: 560px; display: block; margin: 0 auto; }

.dn-about-trust {
    position: relative;
    z-index: 2;
    margin-top: -46px;
    margin-bottom: 60px;
}
.dn-about-trust-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 16px 34px rgba(0,0,0,.08);
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    height: 100%;
}
.dn-about-trust-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(15,85,83,.08);
    color: #0F5553;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.dn-about-trust-card .t { font-weight: 700; font-size: .88rem; color: #14110d; display: block; }
.dn-about-trust-card .s { font-size: .76rem; color: #9a9a92; }
</style>
<section class="dn-about-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 dn-about-hero-text">
                <h1>Daily Needs</h1>
                <h2>Your everyday grocery &amp; essentials solution</h2>
                <p>Shop quality groceries and household products, priced fairly and delivered to your door.</p>
                <div class="dn-about-hero-actions">
                    <a href="/product" class="dn-about-cta">Shop Now</a>
                    <a href="/product" class="dn-about-play" title="Browse products"><i class="ti ti-player-play"></i></a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="/assets/images/pos-computer.png" alt="Daily Needs POS system" class="dn-about-hero-img">
            </div>
        </div>
    </div>
</section>

<div class="container dn-about-trust">
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <div class="dn-about-trust-card">
                <span class="dn-about-trust-icon"><i class="ti ti-shield-check"></i></span>
                <div><span class="t">100% Genuine</span><span class="s">Authentic &amp; trusted brands</span></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dn-about-trust-card">
                <span class="dn-about-trust-icon"><i class="ti ti-user-check"></i></span>
                <div><span class="t">Quality Checked</span><span class="s">Every item inspected</span></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dn-about-trust-card">
                <span class="dn-about-trust-icon"><i class="ti ti-truck"></i></span>
                <div><span class="t">Fast Delivery</span><span class="s">Right to your doorstep</span></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dn-about-trust-card">
                <span class="dn-about-trust-icon"><i class="ti ti-arrow-back-up"></i></span>
                <div><span class="t">Easy Returns</span><span class="s">Hassle-free returns</span></div>
            </div>
        </div>
    </div>
</div>
<!-- Product Cards Section -->
<style>
.dn-cat-products .dn-product-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    height: 100%;
    min-height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 18px;
    background-color: #eae8e2;
    color: #1f2a1f;
    transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
}
.dn-cat-products .dn-product-card:hover { transform: translateY(-4px); box-shadow: 0 14px 28px rgba(0,0,0,.12); }
.dn-cat-products .dn-product-top { display: flex; align-items: flex-start; justify-content: space-between; }
.dn-cat-products .dn-product-eyebrow { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; opacity: .6; }
.dn-cat-products .dn-product-fav {
    width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.55); cursor: pointer; color: #555; font-size: .95rem; flex: 0 0 auto;
}
.dn-cat-products .dn-product-fav.active { color: #d64545; }
.dn-cat-products .dn-product-visual { position: relative; display: flex; align-items: center; justify-content: center; height: 110px; margin: 6px 0; }
.dn-cat-products .dn-product-visual .glow {
    position: absolute; width: 130px; height: 130px; border-radius: 50%;
    background: radial-gradient(circle, rgba(0,0,0,.18) 0%, rgba(0,0,0,0) 70%); filter: blur(2px);
}
.dn-cat-products .dn-product-visual img { position: relative; max-height: 100px; max-width: 85%; object-fit: contain; }
.dn-cat-products .dn-product-name { font-weight: 700; font-size: 1rem; line-height: 1.3; margin-bottom: 4px; cursor: pointer; }
.dn-cat-products .dn-product-tagline { font-size: .78rem; opacity: .65; line-height: 1.4; margin-bottom: 14px; }
.dn-cat-products .dn-product-footer { display: flex; align-items: center; justify-content: space-between; }
.dn-cat-products .dn-product-price { font-weight: 700; font-size: 1.05rem; }
.dn-cat-products .dn-cart-fab {
    width: 34px; height: 34px; border-radius: 50%; border: none;
    background: #1f2a1f; color: #fff; display: flex; align-items: center; justify-content: center;
    transition: background .2s ease-in-out;
}
.dn-cat-products .dn-cart-fab:hover { background: #0F5553; color: #fff; }
</style>
<div class="col-md-9 dn-cat-products">
    <h2 class="mb-4">Our Category</h2>
    <div class="row g-3 px-3 py-4" id="productList">
        <?php
        $dn_about_products = [
            ['id' => 1, 'name' => 'Buldak hot',                    'tag' => 'Snacks',        'tagline' => 'Fiery instant noodles, ready in minutes.', 'img' => 'buldak-hot.png',       'price' => 50.99],
            ['id' => 2, 'name' => 'Good Noodle',                   'tag' => 'Snacks',        'tagline' => 'A quick, satisfying pantry staple.',       'img' => 'good-noodle.png',      'price' => 40.99],
            ['id' => 3, 'name' => 'Mama Pork pack',                'tag' => 'Snacks',        'tagline' => 'Classic pork-flavor instant noodles.',     'img' => 'mama-pork.png',        'price' => 40.99],
            ['id' => 4, 'name' => 'Comfort Blue',                  'tag' => 'Household',     'tagline' => 'Everyday laundry care for the family.',    'img' => 'comfort-blue.png',     'price' => 60.99],
            ['id' => 5, 'name' => 'Fineline Liquid Detergent',     'tag' => 'Household',     'tagline' => 'Deep clean for fabrics, every wash.',      'img' => 'fineline-detergent.png','price' => 55.99],
            ['id' => 6, 'name' => 'Pao Pink Detergent',            'tag' => 'Household',     'tagline' => 'Gentle formula, fresh-smelling clothes.',  'img' => 'pao-pink.png',         'price' => 42.99],
            ['id' => 7, 'name' => 'Keepo Purple',                  'tag' => 'Tissue',        'tagline' => 'Soft, strong, and gentle on your skin.',   'img' => 'keepo-purple.png',     'price' => 48.99],
            ['id' => 8, 'name' => 'Keepo Green',                   'tag' => 'Tissue',        'tagline' => 'Everyday softness for the whole family.',  'img' => 'keepo-green.png',      'price' => 52.99],
            ['id' => 9, 'name' => 'ACNES',                         'tag' => 'Personal Care', 'tagline' => 'Gentle daily care for sensitive skin.',    'img' => 'acnes.png',            'price' => 49.99],
        ];
        ?>
        <?php foreach ($dn_about_products as $p): ?>
            <div class="col-md-4 col-sm-6" data-product-id="<?= $p['id'] ?>">
                <div class="dn-product-card">
                    <div class="dn-product-top">
                        <span class="dn-product-eyebrow"><?= htmlspecialchars($p['tag']) ?></span>
                        <i class="ti ti-heart dn-product-fav" data-heart-id="<?= $p['id'] ?>" onclick="toggleFavorite(<?= $p['id'] ?>)"></i>
                    </div>
                    <div class="dn-product-visual">
                        <div class="glow"></div>
                        <img src="/assets/images/about/<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    </div>
                    <div>
                        <div class="dn-product-name" onclick="viewDetails(<?= $p['id'] ?>)"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="dn-product-tagline"><?= htmlspecialchars($p['tagline']) ?></div>
                        <div class="dn-product-footer">
                            <span class="dn-product-price">$<?= number_format($p['price'], 2) ?></span>
                            <button class="dn-cart-fab" onclick="addToCart(<?= $p['id'] ?>)" title="Add to cart">
                                <i class="ti ti-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

