<?php
$categories = [
    ['label' => 'Household Care', 'count' => '100+ Products', 'href' => '/houeshold', 'img' => 'House Hold Hygiene (11)/Raid.png',            'tint' => '#eaf3e9'],
    ['label' => 'Feminine Care',  'count' => '90+ Products',  'href' => '/feminine',   'img' => 'Feminine Hygiene (10)/Vaseline Original.png', 'tint' => '#fbe9ee'],
    ['label' => 'Oral Health',    'count' => '90+ Products',  'href' => '/oral',       'img' => 'Oral Health (10)/Sensodyne gentle.png',       'tint' => '#e8f1fa'],
    ['label' => 'Beverages',      'count' => '50+ Products',  'href' => '/beverage',   'img' => 'Beverages (6)/Pocari Sweat.png',              'tint' => '#fdf1e3'],
    ['label' => 'Cooking Essentials', 'count' => '150+ Products', 'href' => '/cooking', 'img' => 'Cooking ingredients (20)/Cooking Oil.png',    'tint' => '#fbf6df'],
];

$bestsellers = [
    ['id' => 101, 'name' => 'Pocari Sweat',       'tag' => 'Beverage',      'tagline' => 'Replenish fast, stay refreshed all day.',   'img' => 'Beverages (6)/Pocari Sweat.png',              'price' => 1.00, 'featured' => true],
    ['id' => 102, 'name' => 'Sensodyne Gentle',   'tag' => 'Oral Care',     'tagline' => 'Gentle care for sensitive teeth, daily.',   'img' => 'Oral Health (10)/Sensodyne gentle.png',       'price' => 3.50, 'featured' => false],
    ['id' => 103, 'name' => 'Raid Insect Killer', 'tag' => 'Household',    'tagline' => 'Fast-acting protection for a pest-free home.', 'img' => 'House Hold Hygiene (11)/Raid.png',         'price' => 4.20, 'featured' => false],
    ['id' => 104, 'name' => 'Comfy Tissue',       'tag' => 'Tissue',       'tagline' => 'Soft, strong, and gentle on your skin.',    'img' => 'Tissue (6)/Comfy.png',                        'price' => 2.10, 'featured' => true],
    ['id' => 105, 'name' => 'Vaseline Lip Therapy', 'tag' => 'Personal Care', 'tagline' => 'Essential moisture, all-day comfort.',  'img' => 'Feminine Hygiene (10)/Vaseline Original.png', 'price' => 2.75, 'featured' => false],
    ['id' => 106, 'name' => 'Coca Cola',          'tag' => 'Beverage',      'tagline' => 'Classic taste, ice-cold refreshment.',      'img' => 'Beverages (6)/coca cola.png',                 'price' => 1.20, 'featured' => false],
    ['id' => 107, 'name' => 'Indomie',            'tag' => 'Snacks',        'tagline' => 'Rich, savory noodles ready in minutes.',    'img' => 'Snacks (7)/indomie.png',                      'price' => 0.75, 'featured' => true],
    ['id' => 108, 'name' => 'Knorr Cube Chicken', 'tag' => 'Cooking',       'tagline' => 'Bring rich flavor to every home-cooked meal.', 'img' => 'Cooking ingredients (20)/Knorr Cube Chicken Soup.png', 'price' => 1.50, 'featured' => false],
];
?>
<style>
    .dn-home {
        --dn-dark: #10352f;
        --dn-dark-2: #0b241f;
        --dn-accent: #d98c4a;
        --dn-accent-dark: #bf7638;
        --dn-cream: #faf6f0;
    }
    .dn-home {
        background-color: #f4f3ef;
        background-image: radial-gradient(rgba(0,0,0,.08) 1px, transparent 1px);
        background-size: 16px 16px;
    }
    .dn-home .container { max-width: 1200px; }

    /* Hero */
    .dn-hero {
        background-color: #0F5553;
        border-radius: 0 0 28px 28px;
        padding: 110px 0 90px;
        margin-bottom: 60px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .dn-hero-badge {
        display: inline-block;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.25);
        font-size: .75rem;
        font-weight: 500;
        letter-spacing: .03em;
        padding: 6px 16px;
        border-radius: 30px;
        margin-bottom: 20px;
    }
    .dn-hero h1 {
        font-size: 2.6rem;
        font-weight: 700;
        line-height: 1.25;
        margin-bottom: 18px;
    }
    .dn-hero h1 .accent { color: var(--dn-accent); }
    .dn-hero p.lead {
        color: rgba(255,255,255,.75);
        font-size: 1.05rem;
        margin-bottom: 30px;
        max-width: 440px;
    }
    .dn-btn-accent {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--dn-accent);
        color: #fff;
        padding: 12px 26px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        transition: background .2s ease-in-out;
    }
    .dn-btn-accent:hover { background: var(--dn-accent-dark); color: #fff; }
    .dn-btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        color: #fff;
        border: 1px solid rgba(255,255,255,.5);
        padding: 12px 22px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: background .2s ease-in-out;
    }
    .dn-btn-outline:hover { background: rgba(255,255,255,.12); color: #fff; }

    .dn-hero-visual { position: relative; text-align: center; min-height: 300px; }
    .dn-hero-glow {
        position: absolute;
        inset: 0;
        margin: auto;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(217,140,74,.35) 0%, rgba(217,140,74,0) 70%);
    }
    .dn-hero-visual img.product {
        position: relative;
        max-height: 280px;
        object-fit: contain;
        filter: drop-shadow(0 20px 30px rgba(0,0,0,.35));
    }
    .dn-hero-cert {
        position: absolute;
        bottom: 0;
        right: 8%;
        background: #fff;
        color: var(--dn-dark);
        border-radius: 50%;
        width: 108px;
        height: 108px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: .7rem;
        font-weight: 600;
        line-height: 1.3;
        box-shadow: 0 10px 24px rgba(0,0,0,.25);
    }
    .dn-hero-cert i { font-size: 1.3rem; color: var(--dn-accent); margin-bottom: 4px; }

    /* Trust bar overlapping hero */
    .dn-trust-bar {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 16px 40px rgba(0,0,0,.12);
        margin-top: -60px;
        position: relative;
        z-index: 2;
        padding: 26px 10px;
    }
    .dn-trust-bar .col-6 { border-right: 1px solid #eee; }
    .dn-trust-bar .col-6:last-child,
    .dn-trust-bar .col-md-3:nth-child(4) { border-right: none; }
    .dn-trust-item { display: flex; align-items: center; gap: 12px; justify-content: center; padding: 6px 10px; }
    .dn-trust-item i { font-size: 1.5rem; color: var(--dn-accent); }
    .dn-trust-item .t { font-weight: 600; font-size: .9rem; color: #1f2a1f; display: block; }
    .dn-trust-item .s { font-size: .78rem; color: #888; }

    /* Section heading */
    .dn-section-heading { font-size: 1.55rem; font-weight: 700; color: #1f2a1f; margin: 0; }
    .dn-view-all { color: var(--dn-accent-dark); text-decoration: none; font-weight: 600; font-size: .9rem; display: inline-flex; align-items: center; gap: 4px; }
    .dn-view-all:hover { text-decoration: underline; }

    /* Categories */
    .dn-cat-card {
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        background-color: #eae8e2;
        border-radius: 20px;
        overflow: hidden;
        padding: 18px;
        box-shadow: 0 6px 18px rgba(0,0,0,.06);
        transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
        height: 100%;
    }
    .dn-cat-card:hover { transform: translateY(-4px); box-shadow: 0 14px 28px rgba(0,0,0,.12); }
    .dn-cat-img {
        height: 110px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }
    .dn-cat-img img { max-height: 78%; max-width: 78%; object-fit: contain; }
    .dn-cat-body { padding: 0; }
    .dn-cat-body .name { font-weight: 600; font-size: .95rem; color: #1f2a1f; margin-bottom: 2px; }
    .dn-cat-body .count { font-size: .78rem; color: #999; }

    /* Sale banner */
    .dn-sale-banner {
        background: linear-gradient(120deg, var(--dn-dark) 0%, var(--dn-dark-2) 100%);
        border-radius: 20px;
        color: #fff;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        padding: 32px;
        gap: 16px;
        height: 100%;
    }
    .dn-sale-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,.12);
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .04em;
        padding: 6px 14px;
        border-radius: 20px;
        margin-bottom: 16px;
    }
    .dn-sale-banner h2 { font-size: 1.6rem; font-weight: 700; margin-bottom: 6px; }
    .dn-sale-banner h2 .accent { color: var(--dn-accent); }
    .dn-sale-banner p { color: rgba(255,255,255,.75); margin-bottom: 18px; font-size: .9rem; }
    .dn-sale-image img { max-height: 130px; object-fit: contain; filter: drop-shadow(0 16px 24px rgba(0,0,0,.35)); }

    /* Best sellers */
    .dn-product-card {
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
    .dn-product-card:hover { transform: translateY(-4px); box-shadow: 0 14px 28px rgba(0,0,0,.12); }
    .dn-product-card.featured { background-color: var(--dn-accent); color: #fff; }
    .dn-product-top { display: flex; align-items: flex-start; justify-content: space-between; }
    .dn-product-eyebrow {
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        opacity: .6;
    }
    .dn-product-fav {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.55);
        cursor: pointer;
        color: #555;
        font-size: .95rem;
        flex: 0 0 auto;
    }
    .dn-product-card.featured .dn-product-fav { background: rgba(255,255,255,.25); color: #fff; }
    .dn-product-fav.active { color: #d64545; }
    .dn-product-card.featured .dn-product-fav.active { background: #fff; color: #e0455a; }
    .dn-product-visual { position: relative; display: flex; align-items: center; justify-content: center; height: 110px; margin: 6px 0; }
    .dn-product-visual .glow {
        position: absolute;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(0,0,0,.18) 0%, rgba(0,0,0,0) 70%);
        filter: blur(2px);
    }
    .dn-product-card.featured .dn-product-visual .glow {
        background: radial-gradient(circle, rgba(255,255,255,.3) 0%, rgba(255,255,255,0) 70%);
    }
    .dn-product-visual img { position: relative; max-height: 100px; max-width: 85%; object-fit: contain; }
    .dn-product-name {
        font-weight: 700;
        font-size: 1rem;
        line-height: 1.3;
        margin-bottom: 4px;
        cursor: pointer;
    }
    .dn-product-tagline { font-size: .78rem; opacity: .65; line-height: 1.4; margin-bottom: 14px; }
    .dn-product-footer { display: flex; align-items: center; justify-content: space-between; }
    .dn-product-price { font-weight: 700; font-size: 1.05rem; }
    .dn-cart-fab {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: none;
        background: #1f2a1f;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .2s ease-in-out;
    }
    .dn-product-card.featured .dn-cart-fab { background: #fff; color: var(--dn-accent-dark); }
    .dn-cart-fab:hover { background: var(--dn-accent-dark); color: #fff; }

    /* Bottom perks row */
    .dn-perks-item { display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .dn-perks-item i { font-size: 1.6rem; color: var(--dn-accent-dark); }
    .dn-perks-item strong { font-size: .92rem; color: #1f2a1f; }
    .dn-perks-item span { font-size: .78rem; color: #777; }

    @media (max-width: 767px) {
        .dn-trust-bar .col-6 { border-right: none; border-bottom: 1px solid #eee; padding-bottom: 14px; margin-bottom: 14px; }
        .dn-trust-bar .col-6:nth-last-child(-n+1) { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    }
</style>

<div class="dn-home">

    <!-- Hero -->
    <section class="dn-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="dn-hero-badge">Fresh Picks, Every Day</span>
                    <h1><span style="color:#fff;">Daily Essentials</span><br><span style="color:#fff;">for a</span> <span class="accent">Better Home</span></h1>
                    <p class="lead">Discover quality groceries &amp; household products for a healthier home, delivered to your door.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="/product" class="dn-btn-accent"><i class="ti ti-shopping-cart"></i> Shop Now</a>
                        <a href="#dn-categories" class="dn-btn-outline"><i class="ti ti-layout-grid"></i> Explore Categories</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dn-hero-visual">
                        <div class="dn-hero-glow"></div>
                        <img src="/assets/images/product.png" alt="Featured product" class="product img-fluid">
                        <div class="dn-hero-cert">
                            <i class="ti ti-leaf"></i>
                            100% Genuine Products
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust bar -->
    <div class="container">
        <div class="dn-trust-bar row g-3">
            <div class="col-6 col-md-3">
                <div class="dn-trust-item">
                    <i class="ti ti-shield-check"></i>
                    <div><span class="t">100% Genuine</span><span class="s">Authentic &amp; trusted brands</span></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="dn-trust-item">
                    <i class="ti ti-user-check"></i>
                    <div><span class="t">Quality Checked</span><span class="s">Every item inspected</span></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="dn-trust-item">
                    <i class="ti ti-truck"></i>
                    <div><span class="t">Fast Delivery</span><span class="s">Right to your doorstep</span></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="dn-trust-item">
                    <i class="ti ti-arrow-back-up"></i>
                    <div><span class="t">Easy Returns</span><span class="s">Hassle-free returns</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop by Category -->
    <section id="dn-categories" class="py-5 mt-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="dn-section-heading">Shop by Category</h2>
                <a href="/product" class="dn-view-all">View All Categories <i class="ti ti-chevron-right"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach ($categories as $cat): ?>
                    <div class="col-6 col-md-4 col-lg">
                        <a href="<?= htmlspecialchars($cat['href']) ?>" class="dn-cat-card">
                            <div class="dn-cat-img" style="background:<?= htmlspecialchars($cat['tint']) ?>;">
                                <?php $encCatImg = implode('/', array_map('rawurlencode', explode('/', $cat['img']))); ?>
                                <img src="/assets/images/<?= $encCatImg ?>" alt="<?= htmlspecialchars($cat['label']) ?>">
                            </div>
                            <div class="dn-cat-body">
                                <div class="name"><?= htmlspecialchars($cat['label']) ?></div>
                                <div class="count"><?= htmlspecialchars($cat['count']) ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Sale banner -->
    <section class="py-3">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="dn-sale-banner">
                        <div>
                            <span class="dn-sale-tag"><i class="ti ti-clock"></i> Limited Time Offer</span>
                            <h2><span style="color:#fff;">Up to</span> <span class="accent">20% OFF</span></h2>
                            <p>On top household &amp; wellness brands</p>
                            <a href="/houeshold" class="dn-btn-accent">Shop the Sale <i class="ti ti-arrow-right"></i></a>
                        </div>
                        <div class="dn-sale-image">
                            <?php $encImg404 = implode('/', array_map('rawurlencode', explode('/', 'House Hold Hygiene (11)/Raid.png'))); ?>
                            <img src="/assets/images/<?= $encImg404 ?>" alt="Household sale item">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dn-sale-banner">
                        <div>
                            <span class="dn-sale-tag"><i class="ti ti-clock"></i> Limited Time Offer</span>
                            <h2><span style="color:#fff;">Up to</span> <span class="accent">15% OFF</span></h2>
                            <p>On beverages &amp; everyday snacks</p>
                            <a href="/beverage" class="dn-btn-accent">Shop the Sale <i class="ti ti-arrow-right"></i></a>
                        </div>
                        <div class="dn-sale-image">
                            <?php $encImg417 = implode('/', array_map('rawurlencode', explode('/', 'Beverages (6)/Bear Brand.png'))); ?>
                            <img src="/assets/images/<?= $encImg417 ?>" alt="Beverage sale item">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Best Sellers -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="dn-section-heading">Best Sellers</h2>
                <a href="/product" class="dn-view-all">View All Products <i class="ti ti-chevron-right"></i></a>
            </div>
            <div class="row g-3">
                    <?php foreach ($bestsellers as $p): ?>
                        <div class="col-6 col-lg-3" data-product-id="<?= $p['id'] ?>">
                            <div class="dn-product-card<?= $p['featured'] ? ' featured' : '' ?>">
                                <div class="dn-product-top">
                                    <span class="dn-product-eyebrow"><?= htmlspecialchars($p['tag']) ?></span>
                                    <i class="ti ti-heart dn-product-fav" data-heart-id="<?= $p['id'] ?>" onclick="toggleFavorite(<?= $p['id'] ?>)"></i>
                                </div>
                                <div class="dn-product-visual">
                                    <div class="glow"></div>
                                    <?php $encPImg = implode('/', array_map('rawurlencode', explode('/', $p['img']))); ?>
                                    <img src="/assets/images/<?= $encPImg ?>" alt="<?= htmlspecialchars($p['name']) ?>">
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
    </section>

    <!-- Bottom perks -->
    <section class="py-5" style="background:var(--dn-cream);">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-6 col-md-3">
                    <div class="dn-perks-item">
                        <i class="ti ti-lock"></i>
                        <strong>Secure Payments</strong>
                        <span>100% safe &amp; secure</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dn-perks-item">
                        <i class="ti ti-discount-2"></i>
                        <strong>Exclusive Offers</strong>
                        <span>Member-only savings</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dn-perks-item">
                        <i class="ti ti-headset"></i>
                        <strong>24/7 Support</strong>
                        <span>We're here to help</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dn-perks-item">
                        <i class="ti ti-gift"></i>
                        <strong>Loyalty Rewards</strong>
                        <span>Earn points &amp; get perks</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
