<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to calculate discounted price (same as in viewCart.php)
if (!function_exists('getDiscountedPrice')) {
    function getDiscountedPrice($price, $coupon) {
        if (!$coupon) return $price;
        if ($coupon['discount_type'] === 'percentage') {
            return $price * (1 - $coupon['discount_value'] / 100);
        } else {
            return max(0, $price - $coupon['discount_value']);
        }
    }
}

// Get applied coupon from session
$applied_coupon = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;
?>

<style>
/* Navbar theme — full-width dark teal navbar */
.dn-navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: #0d4a43;
    border-radius: 0;
    box-shadow: 0 2px 12px rgba(0,0,0,.15);
    padding-top: .5rem;
    padding-bottom: .5rem;
    z-index: 1030;
}
.dn-navbar .container { max-width: 1200px; padding-left: 20px; padding-right: 20px; }
.dn-navbar-brand {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.dn-navbar-brand-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    color: #fff;
    flex-shrink: 0;
}
.dn-navbar-brand-icon svg { width: 13px; height: 13px; }
.dn-navbar-brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1.05;
}
.dn-navbar-brand-text strong {
    font-size: .85rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: .01em;
}
.dn-navbar-brand-text span {
    font-size: .52rem;
    font-weight: 600;
    color: rgba(255,255,255,.55);
    text-transform: uppercase;
    letter-spacing: .05em;
}
.header-user-profile .pc-head-link { margin-top: 8px; }

.dn-navbar-links {
    display: flex;
    align-items: center;
    gap: 2px;
}
.dn-navbar .nav-link {
    color: rgba(255,255,255,.8);
    font-weight: 600;
    font-size: .72rem;
    letter-spacing: .01em;
    padding: 5px 10px !important;
    border-radius: 5px;
    white-space: nowrap;
}
.dn-navbar .nav-link:hover,
.dn-navbar .nav-link.active {
    color: #fff !important;
    background: rgba(255,255,255,.12);
}
.dn-navbar .navbar-toggler {
    border-color: rgba(255,255,255,.3);
    padding: .25rem .5rem;
}
.dn-navbar .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}
.dn-nav-cta {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--dn-accent, #d98c4a);
    color: #fff;
    font-weight: 700;
    font-size: .7rem;
    letter-spacing: .02em;
    padding: 7px 14px;
    border-radius: 5px;
    border: none;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s ease-in-out, transform .15s ease-in-out;
}
.dn-nav-cta i { color: #fff; }
.dn-nav-cta:hover { background: #bf7638; color: #fff; transform: translateY(-1px); }

.dn-navbar .icon-cart {
    color: #fff;
}
.dn-navbar .icon-cart i { font-size: 1rem; }

/* Your existing styles remain unchanged */
.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-family: "Nunito", "Kantumruy Pro", sans-serif;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 8px;
}

.cart-items {
    max-height: 300px;
    overflow-y: auto;
    padding-right: 5px;
}

.cart-items::-webkit-scrollbar {
    width: 6px;
}

.cart-items::-webkit-scrollbar-thumb {
    background: #aaa;
    border-radius: 10px;
}

.cart-item img {
    width: 50px;
    height: 50px;
    border-radius: 5px;
    object-fit: cover;
    margin-right: 10px;
}

.cart-item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.cart-item-name {
    font-weight: 600;
    font-size: 14px;
    color: #333;
}

.cart-item-price,
.cart-item-total {
    font-size: 13px;
    color: #666;
}

.cart-item-price .original-price {
    text-decoration: line-through;
    color: #999;
    margin-right: 5px;
}

.cart-item-price .discounted-price {
    color: #28a745;
    font-weight: bold;
}

.delete-icon {
    cursor: pointer;
    color: #888;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f8f8f8;
    transition: 0.2s ease-in-out;
}

.delete-icon:hover {
    background: #dc3545;
    color: #fff;
}

.delete-icon .ti-trash {
    font-size: 14px;
}

.navbar .cart-container .cart-dropdown {
    display: none !important;
    flex-direction: column;
    justify-content: space-between;
    position: absolute;
    top: 54px;
    right: -200px;
    width: 380px;
    height: 80vh;
    background: rgb(209, 206, 206);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 20px;
    z-index: 999;
}

.navbar .cart-container .cart-dropdown.show {
    display: flex !important;
    animation: slideInFromRight 0.5s ease-out forwards;
}

@keyframes slideInFromRight {
    0% { right: -400px; }
    100% { right: -180px; }
}

.cart-subtotal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px 0;
    border-top: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
    font-size: 1.1rem;
    /* Removed invalid line 'fontව' */
}

.nav.drp-tabs.nav-fill .nav-item {
    margin: 0 10px;
}

.nav.drp-tabs.nav-fill .nav-link {
    color: #6c757d;
    border: none;
    background-color: transparent;
    padding: 0.5rem 1rem;
    transition: color 0.3s ease, border-bottom 0.3s ease;
}

.nav.drp-tabs.nav-fill .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
}

.nav.drp-tabs.nav-fill .nav-link:hover {
    color: #0d6efd;
}

.dropdown-user-profile {
    min-width: 300px;
}

.tab-content-layout {
    display: flex;
    gap: 20px;
    padding: 10px;
}

.tab-content-layout .tab-pane {
    flex: 1;
    display: none;
    opacity: 0;
    transform: translateX(20px);
    transition: opacity 0.4s ease-in-out, transform 0.4s ease-in-out;
}

.tab-content-layout .tab-pane.active.show {
    display: block;
    opacity: 1;
    transform: translateX(0);
}

#drp-tab-1 {
    order: 1;
}

#drp-tab-2 {
    order: 2;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.5rem 1rem;
    color: #333;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item i {
    font-size: 1.2rem;
}
</style>

<nav class="navbar navbar-expand-lg dn-navbar px-4">
    <div class="container">
        <a class="navbar-brand fw-bold dn-navbar-brand" href="/">
            <span class="dn-navbar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" aria-label="Daily Needs logo" viewBox="0 0 148 183"><path d="M58.5 9a177 177 0 0 0-34.6 6.1c-13.7 4.5-16.4 8-16.4 21.4 0 17.8 5.2 26.4 19.9 33.5 14.8 7 24.9 8.6 34.8 5.6l4.6-1.3 1.7 3.6c1 2 2 7.3 2.4 11.8.7 9.3 2.2 11.6 7.9 13 3.6.8 3.7 1 6.4 10.3 1.5 5.2 3.7 13.6 4.9 18.7l2.3 9.2-3.8 4.1c-5 5.5-5.8 7.9-4.6 14 1.2 6.5 2.3 8.4 6.3 11.3 7.1 5.3 15.3 6 26 2.2 8.8-3 17.3-8.1 20.4-12.1 6.6-8.6 4.7-32.6-6.3-81.3a128 128 0 0 1-4.1-24c.2-2 2.1-6 4-8.9 2-2.9 3.9-6.8 4.3-8.7 1.4-7.5-2-14.7-9.1-19.7-9.4-6.6-41.9-10.8-67-8.8m37.2 5.5c25.7 3.3 34.3 8.3 34.3 19.6 0 3.7-.8 5.7-4 10.5-5.7 8.5-5.8 11-1.1 30.9a470 470 0 0 1 10.1 54.9l1.2 10.8-4.3 4.4c-8 8.1-23.3 14.3-32.2 13-4.7-.7-10.7-3.9-10.7-5.7 0-.6 1.6-2.9 3.5-5 4.7-5.3 4.6-8.8-.9-29l-4.5-16.2 2-2.5c2.3-3 2.4-5.5.3-13.5-2-7.6-4.7-13.1-7.7-15.4l-2.3-1.7 9.4-4.3c10-4.5 22.7-11.6 23.8-13.4 1.5-2.5-1.2-1.7-13.5 4a313 313 0 0 1-32.4 13.7l-6.8 2.3.8-2.2c.4-1.2.8-6.3.8-11.2 0-7.8-.4-9.7-2.8-14.7a33 33 0 0 0-6.5-8.9A85 85 0 0 0 21.5 21c-1-1.5 21.7-6.1 36-7.3 10.8-.9 27.6-.5 38.2.8M27.3 27.1C41.1 31.7 51 38.2 54.5 44.9c3.7 7.4 4.2 19.9.8 25-1.4 2.1-2 2.3-7.2 1.8A71 71 0 0 1 22.7 62c-6.8-4.3-9.9-10.9-10.5-22.7-.8-14.6 1.8-16.7 15.1-12.2m48.9 47.6c2.5 2.9 4.7 7.9 5.9 13.3 1.3 6.3.6 10-2.1 10-3.1 0-5-3.9-5-10.5-.1-3.3-.7-7.8-1.5-10-1.8-4.9-1.8-4.5-.2-4.5.8 0 2.1.8 2.9 1.7m6.8 4.8q-.1.7-1-.5c-.5-.8-1-2-1-2.5q.1-.8 1 .5c.5.8 1 1.9 1 2.5m1.7 6.7q-.5 1.1-.6-.6t.5-1.3c.3.3.4 1.2.1 1.9m1.4 11.1c-.8.8-1.1-.4-1-4.4l.1-5.4 1 4.4q1 4.4-.1 5.4m49.4 55.1c-1 5.2-7.8 10.8-17.9 14.7s-16.5 4.2-22.2 1a16 16 0 0 1-5.8-5.7c-.8-1.9-1.4-3.4-1.2-3.4l4.7 2c2.5 1.1 6.6 2 9.5 2 8.2 0 22-5.6 29.4-11.8 1.9-1.7 3.6-2.9 3.8-2.7s0 1.9-.3 3.9"></path><path d="M17.9 30.9c-2.1 1.6-2.4 2.7-2.2 7.9.1 7.4 2.6 15.2 5.6 18a80 80 0 0 0 22.1 10.3c4.3 1.1 5 1 7.4-.8s2.7-2.6 2.6-9q-.2-11.2-6.6-16.9c-3.1-2.9-13-7.8-19.7-9.8s-6.4-2-9.2.3m14 6.2c10.6 4.2 14.7 7.7 16.6 14.1q2.8 9.5.2 11.7c-1.7 1.3-8.8-.9-18.5-5.8-5-2.5-6.7-4-7.8-6.8a32 32 0 0 1-2.1-14.7c.9-2.3 2.8-2 11.6 1.5"></path></svg>
            </span>
            <span class="dn-navbar-brand-text">
                <strong>Daily Needs</strong>
                <span>Essentials, one click away</span>
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php $dn_current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
            <ul class="navbar-nav mx-auto dn-navbar-links">
                <li class="nav-item">
                    <a class="nav-link dn-nav-home<?= $dn_current_path === '/' ? ' active' : '' ?>" href="/">SHOP</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $dn_current_path === '/product' ? ' active' : '' ?>" href="/product">PRODUCTS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $dn_current_path === '/product_detail' ? ' active' : '' ?>" href="/product_detail">PRODUCT DETAIL</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $dn_current_path === '/about' ? ' active' : '' ?>" href="/about">ABOUT</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $dn_current_path === '/contact' ? ' active' : '' ?>" href="/contact">CONTACT</a>
                </li>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $dn_current_path === '/admin' ? ' active' : '' ?>" href="/admin">DASHBOARD</a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-4">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a class="dn-nav-cta" href="/F_login">
                        <i class="ti ti-login"></i>
                        <span>Login</span>
                    </a>
                <?php endif; ?>
                <div class="cart-container">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="icon-cart" onclick="toggleCart()">
                            <i class="ti ti-shopping-cart"></i>
                            <span id="cart-count">0</span>
                        </div>
                    <?php endif; ?>
                    <div class="cart-dropdown" id="cartDropdown">
                        <div>
                            <h4>CART</h4>
                            <div class="cart-items"></div>
                        </div>
                        <div class="cart-total">
                            <div class="cart-subtotal" style="display:none;">
                                <span class="subtotal-label">Subtotal:</span>
                                <span class="subtotal-amount">$0.00</span>
                            </div>
                            <a href="/viewcart">
                                <button id="view-cart" class="checkout-btn" style="display: none;">VIEW CART</button>
                            </a>
                            <a href="/checkouts">
                                <button id="checkoutBtn" class="checkout-btn" style="display: none;">CHECKOUT</button>
                            </a>
                            <button id="continueShoppingBtn" class="checkout-btn" style="background:green; display: none;" onclick="window.location.href='/product'">CONTINUE SHOPPING</button>
                        </div>
                    </div>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="ms-auto">
                    <ul class="list-unstyled">
                        <li class="dropdown pc-h-item header-user-profile">
                            <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                                <?php if (!empty($_SESSION['user_profile'])): ?>
                                    <img src="<?php echo htmlspecialchars($_SESSION['user_profile']); ?>" alt="user-image" class="user-avtar" style="width: 28px; height: 28px; border-radius: 50%; border: 2px solid #fff; box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px;" onerror="this.src='/assets/images/user/avatar-2.jpg';">
                                <?php else: ?>
                                    <img src="/assets/images/user/avatar-2.jpg" alt="default-profile-image" class="user-avtar" style="width: 28px; height: 28px; border-radius: 50%; border: 2px solid #fff; box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px;">
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                                <div class="dropdown-header">
                                    <div class="d-flex mb-1">
                                        <div class="flex-shrink-0">
                                            <img src="<?php echo htmlspecialchars($_SESSION['user_profile'] ?? '/assets/images/user/avatar-2.jpg'); ?>" alt="user-image" class="user-avtar wid-35" style="width: 45px; height: 45px; border-radius: 50%; border: 2px solid #fff;" onerror="this.src='/assets/images/user/avatar-2.jpg';">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Guest'); ?></h6>
                                            <span><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'N/A'); ?></span>
                                        </div>
                                        <a href="/logout" class="pc-head-link bg-transparent"><i class="ti ti-power text-danger" style="font-size: 20px;"></i></a>
                                    </div>
                                </div>
                                <ul class="nav drp-tabs nav-fill nav-tabs" id="mydrpTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="drp-t1" data-bs-toggle="tab" data-bs-target="#drp-tab-1" type="button" role="tab" aria-controls="drp-tab-1" aria-selected="true">
                                            <i class="ti ti-user"></i> Profile
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="drp-t2" data-bs-toggle="tab" data-bs-target="#drp-tab-2" type="button" role="tab" aria-controls="drp-tab-2" aria-selected="false">
                                            <i class="ti ti-settings"></i> Setting
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content-layout">
                                    <div class="tab-pane fade active show" id="drp-tab-1" role="tabpanel" aria-labelledby="drp-t1" tabindex="0">
                                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                            <a href="/admin" class="dropdown-item"><i class="ti ti-dashboard"></i><span>Admin Dashboard</span></a>
                                        <?php endif; ?>
                                        <a href="#!" class="dropdown-item"><i class="ti ti-edit-circle"></i><span>Edit Profile</span></a>
                                        <a href="#!" class="dropdown-item"><i class="ti ti-user"></i><span>View Profile</span></a>
                                        <a href="#!" class="dropdown-item"><i class="ti ti-clipboard-list"></i><span>Social Profile</span></a>
                                        <a href="#!" class="dropdown-item"><i class="ti ti-wallet"></i><span>Billing</span></a>
                                        <a href="/logout" class="dropdown-item"><i class="ti ti-power"></i><span>Logout</span></a>
                                    </div>
                                    <div class="tab-pane fade" id="drp-tab-2" role="tabpanel" aria-labelledby="drp-t2" tabindex="0">
                                        <a href="#!" class="dropdown-item"><i class="ti ti-help"></i><span>Support</span></a>
                                        <a href="#!" class="dropdown-item"><i class="ti ti-user"></i><span>Account Settings</span></a>
                                        <a href="#!" class="dropdown-item"><i class="ti ti-lock"></i><span>Privacy Center</span></a>
                                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'users'): ?>
                                            <a href="#!" class="dropdown-item"><i class="ti ti-messages"></i><span>Feedback</span></a>
                                            <a href="/order_h" class="dropdown-item"><i class="ti ti-list"></i><span>History</span></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="dn-modal-overlay" id="loginPromptOverlay">
    <div class="dn-modal-card">
        <i class="ti ti-lock dn-modal-icon"></i>
        <h5>Login Required</h5>
        <p id="loginPromptMessage">Please log in to continue.</p>
        <div class="dn-modal-actions">
            <button type="button" class="dn-modal-btn-outline" id="loginPromptCancel">Cancel</button>
            <a href="/F_login" class="dn-modal-btn-accent">Log In</a>
        </div>
    </div>
</div>

<style>
    .dn-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 2000;
        align-items: center;
        justify-content: center;
    }
    .dn-modal-overlay.show { display: flex; }
    .dn-modal-card {
        background: #fff;
        border-radius: 14px;
        padding: 28px;
        max-width: 340px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,.25);
    }
    .dn-modal-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #f4f3ef;
        color: #003FA5;
        font-size: 1.4rem;
        margin-bottom: 14px;
    }
    .dn-modal-card h5 { font-weight: 700; color: #1f2a1f; margin-bottom: 8px; }
    .dn-modal-card p { color: #777; font-size: .9rem; margin-bottom: 22px; }
    .dn-modal-actions { display: flex; gap: 10px; justify-content: center; }
    .dn-modal-btn-outline {
        padding: 9px 18px;
        border-radius: 8px;
        border: 1px solid #eee;
        background: #fff;
        color: #555;
        font-weight: 600;
        font-size: .85rem;
        cursor: pointer;
    }
    .dn-modal-btn-accent {
        padding: 9px 18px;
        border-radius: 8px;
        border: none;
        background: #003FA5;
        color: #fff;
        font-weight: 600;
        font-size: .85rem;
        text-decoration: none;
    }
    .dn-modal-btn-accent:hover { background: #032e7a; color: #fff; }

    .nav.drp-tabs.nav-fill .nav-item {
        margin: 0 10px; /* Adds space between the tabs */
    }

    .nav.drp-tabs.nav-fill .nav-link {
        color: #6c757d; /* Default gray color */
        border: none;
        background-color: transparent;
        padding: 0.5rem 1rem;
        transition: color 0.3s ease, border-bottom 0.3s ease; /* Smooth transition for hover and active states */
    }

    .nav.drp-tabs.nav-fill .nav-link.active {
        color: #0d6efd; /* Blue color for active tab */
        border-bottom: 2px solid #0d6efd; /* Underline effect for active tab */
    }

    .nav.drp-tabs.nav-fill .nav-link:hover {
        color: #0d6efd; /* Blue color on hover */
    }

    /* Ensure the dropdown menu has enough width to accommodate the tabs */
    .dropdown-user-profile {
        min-width: 300px; /* Adjust based on your content */
    }

    /* Container for the tab content layout */
    .tab-content-layout {
        display: flex;
        gap: 20px; /* Space between the two columns */
        padding: 10px;
    }

    /* Smooth transition for tab content */
    .tab-content-layout .tab-pane {
        flex: 1; /* Each pane takes equal width */
        display: none; /* Hide all panes by default */
        opacity: 0;
        transform: translateX(20px); /* Start slightly to the right */
        transition: opacity 0.4s ease-in-out, transform 0.4s ease-in-out; /* Smooth fade and slide effect */
    }

    .tab-content-layout .tab-pane.active {
        display: block; /* Show active pane */
        opacity: 1;
        transform: translateX(0); /* Move to original position */
    }

    /* Left column for Profile */
    #drp-tab-1 {
        order: 1; /* Ensure Profile is on the left */
    }

    /* Right column for Setting */
    #drp-tab-2 {
        order: 2; /* Ensure Setting is on the right */
    }

    /* Additional styling for dropdown items */
    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px; /* Space between icon and text */
        padding: 0.5rem 1rem;
        color: #333;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa; /* Light background on hover */
    }

    .dropdown-item i {
        font-size: 1.2rem; /* Icon size */
    }
    .navbar .cart-container .cart-dropdown {
        display: none !important;
        flex-direction: column;
        justify-content: space-between;
        position: absolute;
        top: 54px;
        right: -200px;
        width: 380px;
        height: 80vh; /* Full height of the viewport */
        background: rgb(209, 206, 206);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 20px;
        z-index: 999;
    }

    .navbar .cart-container .cart-dropdown.show {
        display: flex !important;
        animation: slideInFromRight 0.5s ease-out forwards; /* Animation applied when .show is added */
    }

    /* Define the animation */
    @keyframes slideInFromRight {
        0% {
            right: -400px; /* Start off-screen to the right (beyond the width of the cart) */
        }
        100% {
            right: -180px; /* End at the final position */
        }
    }

    .cart-subtotal {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px 0;
        border-top: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        font-size: 1.1rem;
        font-weight: bold;
        color: #333;
    }

    .subtotal-label {
        text-transform: uppercase;
    }

    .subtotal-amount {
        color: #000;
    }

</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const loginPromptOverlay = document.getElementById('loginPromptOverlay');
    const loginPromptMessage = document.getElementById('loginPromptMessage');
    const loginPromptCancel = document.getElementById('loginPromptCancel');

    window.showLoginPrompt = function (message) {
        if (!loginPromptOverlay) return;
        if (loginPromptMessage && message) loginPromptMessage.textContent = message;
        loginPromptOverlay.classList.add('show');
    };

    if (loginPromptCancel && loginPromptOverlay) {
        loginPromptCancel.addEventListener('click', function () {
            loginPromptOverlay.classList.remove('show');
        });
        loginPromptOverlay.addEventListener('click', function (e) {
            if (e.target === loginPromptOverlay) loginPromptOverlay.classList.remove('show');
        });
    }

    const tabButtons = document.querySelectorAll('.nav.drp-tabs .nav-link');
    const tabPanes = document.querySelectorAll('.tab-content-layout .tab-pane');
    tabButtons.forEach((button) => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => {
                pane.classList.remove('active');
                pane.classList.remove('show');
            });
            this.classList.add('active');
            const targetPaneId = this.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetPaneId);
            if (targetPane) {
                targetPane.classList.add('active');
                targetPane.classList.add('show');
            }
        });
    });

    window.addToCart = async function(productId) {
        if (!productId) {
            alert('Invalid product ID');
            return;
        }
        try {
            console.log('Adding product ID:', productId);
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${encodeURIComponent(productId)}&quantity=1`
            });
            if (!response.ok) throw new Error('Network response was not ok');
            const result = await response.json();
            console.log('Add to cart response:', result);
            if (result.success) {
                await fetchCartItems();
            } else {
                if (result.message === 'User not logged in') {
                    showLoginPrompt('Please log in to add items to your cart.');
                } else {
                    alert(result.message || 'Failed to add item to cart');
                }
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            alert('An error occurred while adding to cart. Please try again.');
        }
    };

    window.removeFromCart = async function(productId) {
        if (!productId) {
            alert('Invalid product ID');
            return;
        }
        try {
            const response = await fetch('/cart/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${encodeURIComponent(productId)}`
            });
            if (!response.ok) throw new Error('Network response was not ok');
            const result = await response.json();
            console.log('Remove from cart response:', result);
            if (result.success) {
                await fetchCartItems();
            } else {
                alert(result.message || 'Failed to remove item from cart');
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
            alert('An error occurred while removing from cart. Please try again.');
        }
    };

    async function fetchCartItems() {
        try {
            const response = await fetch('/cart/items');
            if (!response.ok) throw new Error('Network response was not ok');
            const result = await response.json();
            console.log('Fetch cart items response:', result);
            if (result.success) {
                updateCartDropdown(result.data || []);
                updateCartCount(result.data || []);
            } else {
                console.warn('Failed to fetch cart items:', result.message);
                updateCartDropdown([]);
                updateCartCount([]);
            }
        } catch (error) {
            console.error('Error fetching cart items:', error);
            updateCartDropdown([]);
            updateCartCount([]);
        }
    }

    function updateCartCount(cartItems) {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            const count = Array.isArray(cartItems) 
                ? cartItems.reduce((total, item) => total + (parseInt(item.quantity) || 0), 0)
                : 0;
            cartCountElement.textContent = count;
        }
    }

    function updateCartDropdown(cartItems) {
        const cartDropdown = document.getElementById('cartDropdown');
        if (!cartDropdown) return;

        const cartItemsContainer = cartDropdown.querySelector('.cart-items');
        const subtotalSection = cartDropdown.querySelector('.cart-subtotal');
        const subtotalAmount = cartDropdown.querySelector('.subtotal-amount');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const continueShoppingBtn = document.getElementById('continueShoppingBtn');
        const viewCartBtn = document.getElementById('view-cart');

        cartItemsContainer.innerHTML = '';

        if (!Array.isArray(cartItems) || cartItems.length === 0) {
            cartItemsContainer.innerHTML = '<p>No products in the cart.</p>';
            if (subtotalSection) subtotalSection.style.display = 'none';
            if (viewCartBtn) viewCartBtn.style.display = 'none';
            if (checkoutBtn) checkoutBtn.style.display = 'none';
            if (continueShoppingBtn) continueShoppingBtn.style.display = 'block';
            return;
        }

        const appliedCoupon = <?php echo json_encode($applied_coupon); ?>;
        let subtotal = 0;

        cartItems.forEach(item => {
            if (!item || !item.product_id) return;

            const price = parseFloat(item.price) || 0;
            const quantity = parseInt(item.quantity) || 1;
            const discountedPrice = getDiscountedPrice(price, appliedCoupon);
            const itemTotal = discountedPrice * quantity;
            subtotal += itemTotal;

            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <img src="${item.imageURL || '/default-image.jpg'}" alt="${item.productname || 'Product'}">
                <div class="cart-item-details">
                    <span class="cart-item-name">${item.productname || 'Unknown Product'}</span>
                    <span class="cart-item-price">
                        ${appliedCoupon && discountedPrice < price ? 
                            `<span class="original-price">$${price.toFixed(2)}</span> <span class="discounted-price">$${discountedPrice.toFixed(2)}</span>` : 
                            `$${price.toFixed(2)}`} x ${quantity}
                    </span>
                    <span class="cart-item-total">$${itemTotal.toFixed(2)}</span>
                </div>
                <span class="delete-icon" onclick="removeFromCart(${item.product_id})">
                    <i class="ti ti-trash"></i>
                </span>
            `;
            cartItemsContainer.appendChild(cartItem);
        });

        if (subtotalSection && subtotalAmount) {
            subtotalSection.style.display = 'flex';
            subtotalAmount.textContent = `$${subtotal.toFixed(2)}`;
        }
        if (viewCartBtn) viewCartBtn.style.display = 'block';
        if (checkoutBtn) checkoutBtn.style.display = 'block';
        if (continueShoppingBtn) continueShoppingBtn.style.display = 'none';
    }

    window.toggleCart = function() {
        const cartDropdown = document.getElementById('cartDropdown');
        if (!cartDropdown) {
            console.error('Cart dropdown element not found');
            return;
        }
        const isDisplayed = cartDropdown.classList.contains('show');
        if (!isDisplayed) {
            cartDropdown.classList.add('show');
            fetchCartItems();
        } else {
            cartDropdown.classList.remove('show');
        }
    };

    const cartDropdown = document.getElementById('cartDropdown');
    if (cartDropdown) cartDropdown.classList.remove('show');

    <?php if (isset($_SESSION['user_id'])): ?>
        fetchCartItems();
    <?php endif; ?>

    function getDiscountedPrice(price, coupon) {
        if (!coupon) return price;
        if (coupon.discount_type === 'percentage') {
            return price * (1 - coupon.discount_value / 100);
        } else {
            return Math.max(0, price - coupon.discount_value);
        }
    }
});
</script>