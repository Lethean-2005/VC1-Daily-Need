<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) : 
    $order = $data['order'] ?? null;
    $orderItems = $data['orderItems'] ?? [];
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
    .card {
        margin: 0 auto;
        background: rgb(238,174,202);
        background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(207,183,205,1) 0%, rgba(209,209,209,1) 0%, rgba(182,207,192,1) 100%, rgba(156,174,216,1) 100%, rgba(61,18,61,1) 100%, rgba(174,193,209,1) 100%, rgba(115,65,212,1) 100%, rgba(98,217,218,1) 100%, rgba(85,87,120,1) 100%, rgba(243,215,251,1) 100%);
    }
    .img-item {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .product-image {
        width: 100px !important;
        height: 100px !important; 
        object-fit: contain;  
        border-radius: 8px; 
    }
    .img-item div {
        display: flex;
        flex-direction: column; 
        width: 100%; 
    }
    .img-item h6, 
    .img-item p {
        min-width: 120px; 
    }
    .product-image:hover {
        transform: scale(1.1);
    }
    .product-image {
        transition: transform 0.3s ease-out, box-shadow 0.3s ease-out;
        max-width: 100px;
    }
    .img-item {
        align: center;
        width: 90%;
        margin-left: 5% !important;
        border: 1px solid;
        border-color: blue rgba(170, 50, 220, 0.6) green;
        transition: border-color 0.2s ease-in-out, transform 0.2s ease-in-out;
        border-radius: 25px;
    }
    .img-item:hover {
        border-color: red rgba(50, 170, 220, 0.6) yellow;
        transform: scale(1.05);
    }
    .product-image:hover {
        transform: scale(1.2);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    html, body {
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;  
    }
    body {
        height: 100%;
        background: rgb(207,183,205);
        background: radial-gradient(circle, rgba(207,183,205,1) 0%, rgba(255,255,255,1) 0%, rgba(245,174,205,1) 0%, rgba(182,207,192,1) 100%, rgba(156,174,216,1) 100%, rgba(61,18,61,1) 100%, rgba(174,193,209,1) 100%, rgba(115,65,212,1) 100%, rgba(98,217,218,1) 100%, rgba(85,87,120,1) 100%, rgba(243,215,251,1) 100%);
    }
</style>

<div class="container my-5">
    <h2 class="mb-4">ORDER CONFIRMATION</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Thank You for Your Order! 😊</h5>
            <p style="color:green;">Your order has been successfully placed 🎉. You will receive a confirmation email shortly 😍.</p>
            <?php if ($order && !empty($orderItems)): ?>
                <h6>Order Details</h6>
                <p>Order ID: <strong><?php echo htmlspecialchars($order['id']); ?></strong></p>
                <p>Order Date:<strong> <?php echo htmlspecialchars($order['orderdate']); ?></strong></p>
                <p>Order Status:<strong> <?php echo htmlspecialchars($order['orderstatus']); ?></strong></p>
                <p>Shipping Location:<strong> <?php echo htmlspecialchars($order['location_name']); ?></strong></p>
                <p>Total:<strong> 
                    <?php echo $order['payments'] === 'KH Riel' ? number_format($subtotal * 4000) . ' KH Riel' : '$' . number_format($subtotal, 2); ?></strong>
                </p>
                <hr>
                <h6>Order Items</h6>
                <?php foreach ($orderItems as $item): ?>
                    <div class="d-flex align-items-center mb-3 img-item">
                        <img src="<?php echo htmlspecialchars($item['imageURL']); ?>" 
                            alt="<?php echo htmlspecialchars($item['productname']); ?>" 
                            class="img-fluid rounded me-3 product-image">
                        <div>
                            <h6><?php echo htmlspecialchars($item['productname']); ?></h6>
                            <p class="text-muted mb-0">QUANTITY: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            <p class="text-muted mb-0">
                                PRICE: <?php echo $order['payments'] === 'KH Riel' ? number_format($item['price'] * 4000) . ' KH Riel' : '$' . number_format($item['price'], 2); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    No order details available.
                </div>
            <?php endif; ?>
            <a href="/product" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
</div>
<?php else: ?>
    <?php $this->redirect("/F_login"); ?>
<?php endif; ?>