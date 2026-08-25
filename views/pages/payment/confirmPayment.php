<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) : 
    $currency = $data['currency'] ?? 'USD';
    $order = $data['order'] ?? null;
    $orderItems = $data['orderItems'] ?? [];
    $payment_method = $data['payment_method'] ?? null;
    $payment_type = $data['payment_type'] ?? 'Unknown';
    $error = $data['error'] ?? null;

    // Function to calculate discounted price (same as in checkout.php)
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

    // Recalculate subtotal from order items (excluding shipping)
    $subtotal = 0;
    foreach ($orderItems as $item) {
        $price = floatval($item['price'] ?? 0);
        $quantity = intval($item['quantity'] ?? 1);
        $discounted_price = getDiscountedPrice($price, $applied_coupon);
        $subtotal += $discounted_price * $quantity;
    }
?>

<style>
    body {
        background: rgb(235, 235, 235);
        background: radial-gradient(circle, rgb(255, 255, 255) 0%, rgb(247, 247, 247) 0%, rgba(174,193,209,1) 100%, rgba(98,217,218,1) 100%);
    }
    html, body {
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;  
    }
    .product-image {
        width: 100px !important;
        height: 100px !important; 
        object-fit: contain; 
        border-radius: 8px; 
        transition: transform 0.3s ease-out;
    }
    .product-image:hover {
        transform: scale(1.1);
    }
    .card-body {
        background: rgb(238,174,202);
        background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(174,193,209,1) 100%, rgba(115,65,212,1) 100%, rgba(98,217,218,1) 100%, rgba(85,87,120,1) 100%, rgba(243,215,251,1) 100%);
        border-radius: 5px;
    }
    .btn-success {
        background: blue !important;
    }
    .btn-success:hover {
        background: green !important;
    }
    .im-item {
        border: 1px solid #fff;
        border-color: blue rgba(170, 50, 220, 0.6) green;
        border-radius: 25px;
    }
    .bg {
        animation: slide 3s ease-in-out infinite alternate;
        background-image: linear-gradient(-60deg, #6c3 50%, #09f 50%);
        bottom: 0;
        left: -50%;
        opacity: .5;
        position: fixed;
        right: -50%;
        top: 0;
        z-index: -1;
    }
    .bg2 {
        animation-direction: alternate-reverse;
        animation-duration: 4s;
    }
    .bg3 {
        animation-duration: 5s;
    }
    .content {
        background-color: rgba(255,255,255,.8);
        border-radius: .25em;
        box-shadow: 0 0 .25em rgba(0,0,0,.25);
        box-sizing: border-box;
        left: 50%;
        padding: 10vmin;
        position: fixed;
        text-align: center;
        top: 50%;
        transform: translate(-50%, -50%);
    }
    h1 {
        font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
    }
    @keyframes slide {
        0% {
            transform: translateX(-25%);
        }
        100% {
            transform: translateX(25%);
        }
    }
</style>

<div class="bg"></div>
<div class="bg bg2"></div>
<div class="bg bg3"></div>

<div class="container my-5">
    <h2 class="mb-4" style="color:white">PAYMENT CONFIRMATION</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="color:green">ORDER SUMMARY</h5>
                    <?php if ($order && !empty($orderItems)): ?>
                        <?php foreach ($orderItems as $item): ?>
                            <div class="d-flex align-items-center mb-3 im-item">
                                <img src="<?php echo htmlspecialchars($item['imageURL']); ?>" 
                                    alt="<?php echo htmlspecialchars($item['productname']); ?>" 
                                    class="img-fluid rounded me-3 product-image">
                                <div class="flex-grow-1">
                                    <h6><?php echo htmlspecialchars($item['productname']); ?></h6>
                                    <p class="text-muted mb-0">
                                        QUANTITY: <?php echo htmlspecialchars($item['quantity']); ?>
                                    </p>
                                    <p class="text-muted mb-0">
                                        PRICE: <?php echo $currency === 'KH Riel' ? number_format($item['price'] * 4000) . ' KH Riel' : '$' . number_format($item['price'], 2); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <p>SHIPPING LOCATION:<strong> <?php echo htmlspecialchars($order['location_name']); ?></strong></p>
                        <p>ORDER STATUS:<strong> <?php echo htmlspecialchars($order['orderstatus']); ?></strong></p>
                        <p class="">TOTAL:<strong> 
                            <?php echo $currency === 'KH Riel' ? number_format($subtotal * 4000) . ' KH Riel' : '$' . number_format($subtotal, 2); ?></strong>
                        </p>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            No order selected. <a href="/products" class="alert-link">Go back to products</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Confirm Payment Form -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="color:green">CONFIRM PAYMENT</h5>
                    <?php
                    $effective_payment_type = isset($_SESSION['confirm_data']['payment_type']) && !empty($_SESSION['confirm_data']['payment_type']) 
                        ? $_SESSION['confirm_data']['payment_type'] 
                        : ($payment_type !== 'Unknown' ? $payment_type : 'Unknown');
                    $effective_payment_method = $_SESSION['confirm_data']['payment_method'] ?? $payment_method;
                    error_log("confirm.php - Effective payment_type: $effective_payment_type");
                    ?>
                    <?php if ($effective_payment_type !== 'Unknown' && ($effective_payment_method || $effective_payment_type === 'Paypal')): ?>
                        <p>PAYMENT METHOD:<strong> 
                            <?php 
                            echo htmlspecialchars($effective_payment_type); 
                            if ($effective_payment_type !== 'Paypal' && $effective_payment_method && isset($effective_payment_method['card_number']) && !empty($effective_payment_method['card_number'])) {
                                echo " (ending in " . htmlspecialchars(substr($effective_payment_method['card_number'], -4)) . ")";
                            } elseif ($effective_payment_type === 'Paypal') {
                                echo " (PayPal)";
                            }
                            ?>
                        </strong></p>
                        <?php if ($effective_payment_type !== 'Paypal' && $effective_payment_method): ?>
                            <p>CARDHOLDER NAME:<strong> <?php echo htmlspecialchars($effective_payment_method['card_holder_name'] ?? 'N/A'); ?></strong></p>
                            <p>EXPIRY DATE:<strong> <?php echo htmlspecialchars($effective_payment_method['expiry_date'] ?? 'N/A'); ?></strong></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-danger">No payment method found.</p>
                    <?php endif; ?>
                    <form action="/confirm-payment" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id'] ?? ''); ?>">
                        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($subtotal); ?>">
                        <input type="hidden" name="payment_type" value="<?php echo htmlspecialchars($effective_payment_type); ?>">
                        <button type="submit" class="btn btn-success w-100">Confirm Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
    <?php header("Location: /F_login"); exit; ?>
<?php endif; ?>