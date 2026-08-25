<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartItems = isset($data['cartItems']) ? $data['cartItems'] : [];
$error = isset($data['error']) ? $data['error'] : null;

// Check for applied coupon
$applied_coupon = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;

function getDiscountedPrice($price, $coupon) {
    if (!$coupon) return $price;
    if ($coupon['discount_type'] === 'percentage') {
        return $price * (1 - $coupon['discount_value'] / 100);
    } else {
        return max(0, $price - $coupon['discount_value']);
    }
}

// Calculate subtotal and total with discount
$subtotal = 0;
foreach ($cartItems as $item) {
    $price = floatval($item['price'] ?? 0);
    $quantity = intval($item['quantity'] ?? 1);
    $discounted_price = getDiscountedPrice($price, $applied_coupon);
    $subtotal += $discounted_price * $quantity;
}
$total = $subtotal; // Add taxes or other fees if needed
?>

<div class="container my-5 p-4 bg-white shadow-lg rounded">
    <h2 class="fw-bold mb-4">Cart</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Cart Table -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 5%;"></th>
                    <th scope="col">Product</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cartItems)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No items in the cart.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <?php
                        $productId = htmlspecialchars($item['product_id'] ?? '');
                        $productName = htmlspecialchars($item['productname'] ?? 'Unknown Product');
                        $imageURL = htmlspecialchars($item['imageURL'] ?? 'https://via.placeholder.com/50');
                        $price = floatval($item['price'] ?? 0);
                        $quantity = intval($item['quantity'] ?? 1);
                        $stockQuantity = intval($item['stockquantity'] ?? 0);
                        $discounted_price = getDiscountedPrice($price, $applied_coupon);
                        $itemSubtotal = $discounted_price * $quantity;
                        $outOfStockWarning = $quantity > $stockQuantity ? ' <span class="text-danger">(Only ' . $stockQuantity . ' in stock)</span>' : '';
                        ?>
                        <tr data-product-id="<?php echo $productId; ?>" data-stock="<?php echo $stockQuantity; ?>">
                            <td class="text-center">
                                <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(<?php echo $productId; ?>)">×</button>
                            </td>
                            <td class="d-flex align-items-center">
                                <img src="<?php echo $imageURL; ?>" alt="<?php echo $productName; ?>" class="me-2" style="width: 50px; height: 50px;">
                                <a href="#" class="text-success text-decoration-none"><?php echo $productName; ?></a>
                            </td>
                            <td>
                                <?php
                                if ($applied_coupon && $discounted_price < $price) {
                                    echo "<del>$" . number_format($price, 2) . "</del> $" . number_format($discounted_price, 2);
                                } else {
                                    echo "$" . number_format($price, 2);
                                }
                                ?>
                            </td>
                            <td>
                                <input type="number" class="form-control w-50 quantity-input" value="<?php echo $quantity; ?>" min="1" max="<?php echo $stockQuantity; ?>" onchange="updateQuantity(<?php echo $productId; ?>, this.value, this)">
                                <?php echo $outOfStockWarning; ?>
                            </td>
                            <td class="item-subtotal">$<?php echo number_format($itemSubtotal, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Coupon Code Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Coupon code" id="couponCode" <?php echo $applied_coupon ? 'disabled' : ''; ?>>
                <button class="btn btn-success" type="button" onclick="applyCoupon()" <?php echo $applied_coupon ? 'disabled' : ''; ?>>Apply Coupon</button>
                <?php if ($applied_coupon): ?>
                    <button class="btn btn-outline-danger ms-2" type="button" onclick="clearCoupon()">Clear</button>
                <?php endif; ?>
            </div>
            <?php if ($applied_coupon): ?>
                <div class="mt-2">
                    <small class="text-success">Applied Coupon: <?php echo htmlspecialchars($applied_coupon['code']); ?> 
                        (<?php echo $applied_coupon['discount_type'] === 'percentage' ? $applied_coupon['discount_value'] . '%' : '$' . $applied_coupon['discount_value']; ?> off)
                    </small>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-outline-success" type="button" onclick="updateCart()">Update Cart</button>
        </div>
    </div>

    <!-- Cart Totals -->
    <div class="d-flex justify-content-end">
        <div class="card p-3" style="width: 50%;">
            <h4 class="fw-bold">Cart Totals<hr></h4>
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <th><hr>Subtotal <hr></th>
                        <td class="subtotal"><hr>$<?php echo number_format($subtotal, 2); ?> <hr></td>
                    </tr>
                    <tr>
                        <th><hr>Total <hr></th>
                        <td class="total"><hr>$<?php echo number_format($total, 2); ?> <hr></td>
                    </tr>
                </tbody>
            </table>
            <a href="/checkouts">
                <button class="btn btn-success btn-lg w-100">Proceed to Checkout</button>
            </a>
        </div>
    </div>
</div>

<script>
async function removeFromCart(productId) {
    if (!productId) {
        alert('Invalid product ID');
        return;
    }

    try {
        const response = await fetch('/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${encodeURIComponent(productId)}`
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();
        if (result.success) {
            alert('Item removed from cart');
            window.location.reload();
        } else {
            alert(result.message || 'Failed to remove item from cart');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        alert('An error occurred while removing from cart. Please try again.');
    }
}

async function updateQuantity(productId, quantity, inputElement) {
    if (!productId || quantity < 1) {
        alert('Invalid product ID or quantity');
        inputElement.value = inputElement.defaultValue;
        return;
    }

    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    const stockQuantity = parseInt(row.dataset.stock) || 0;
    const originalQuantity = parseInt(inputElement.defaultValue) || 1;

    if (quantity > stockQuantity) {
        alert(`Only ${stockQuantity} items available in stock`);
        inputElement.value = stockQuantity;
        quantity = stockQuantity;
    }

    try {
        console.log('Sending update request:', { productId, quantity });
        const response = await fetch('/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${encodeURIComponent(productId)}&quantity=${encodeURIComponent(quantity)}`
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }

        const result = await response.json();
        if (result.success) {
            const cartResponse = await fetch('/cart/items');
            if (!cartResponse.ok) {
                throw new Error('Failed to fetch updated cart items');
            }
            const cartResult = await cartResponse.json();
            if (!cartResult.success) {
                throw new Error('Failed to fetch updated cart items: ' + (cartResult.message || 'Unknown error'));
            }

            const cartItems = cartResult.data;
            if (row) {
                const quantityInput = row.querySelector('.quantity-input');
                quantityInput.value = quantity;
                quantityInput.defaultValue = quantity;

                const priceText = row.querySelector('td:nth-child(3)').textContent;
                const price = parseFloat(priceText.includes('<del>') ? priceText.match(/\$([\d.]+)/g)[1] : priceText.replace('$', '')) || 0;
                const itemSubtotal = price * quantity;
                const itemSubtotalCell = row.querySelector('.item-subtotal');
                itemSubtotalCell.textContent = `$${itemSubtotal.toFixed(2)}`;
            }

            updateTotals(cartItems);
        } else {
            if (result.message === 'Not enough stock available') {
                alert('Not enough stock available');
                inputElement.value = stockQuantity;
                inputElement.defaultValue = stockQuantity;
            } else {
                inputElement.value = originalQuantity;
            }
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        inputElement.value = originalQuantity;
    }
}

async function applyCoupon() {
    const couponInput = document.getElementById('couponCode');
    const couponCode = couponInput.value.trim();

    if (!couponCode) {
        alert('Please enter a coupon code');
        return;
    }

    try {
        const response = await fetch('/cart/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `coupon_code=${encodeURIComponent(couponCode)}`
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Coupon response:', result);

        if (result.success) {
            const discountQuantity = result.discount_type === 'percentage' 
                ? `${result.discount_value}%` 
                : `$${result.discount_value}`;
            alert(`Congratulations, you got ${discountQuantity} discount now!`);
            window.location.reload(); // Reload to reflect applied coupon
        } else {
            alert(result.message || 'Invalid coupon code');
        }
    } catch (error) {
        console.error('Error applying coupon:', error);
        alert('An error occurred while applying the coupon. Please try again.');
    }
}

async function clearCoupon() {
    try {
        const response = await fetch('/cart/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `coupon_code=` // Sending empty coupon code to clear it
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();
        if (result.success || result.message === 'Invalid or unauthorized coupon code') {
            alert('Coupon cleared');
            window.location.reload();
        } else {
            alert('Failed to clear coupon');
        }
    } catch (error) {
        console.error('Error clearing coupon:', error);
        alert('An error occurred while clearing the coupon. Please try again.');
    }
}

function updateCart() {
    alert('Cart updated');
    window.location.reload();
}

function updateTotals(cartItems) {
    let newSubtotal = 0;
    cartItems.forEach(item => {
        const price = parseFloat(item.price) || 0;
        const qty = parseInt(item.quantity) || 1;
        const discountedPrice = <?php echo $applied_coupon ? "getDiscountedPrice(price, " . json_encode($applied_coupon) . ")" : 'price'; ?>;
        newSubtotal += discountedPrice * qty;
    });

    const subtotalElement = document.querySelector('.subtotal');
    const totalElement = document.querySelector('.total');
    subtotalElement.innerHTML = `<hr>$${newSubtotal.toFixed(2)} <hr>`;
    totalElement.innerHTML = `<hr>$${newSubtotal.toFixed(2)} <hr>`;
}

function getDiscountedPrice(price, coupon) {
    if (!coupon) return price;
    if (coupon.discount_type === 'percentage') {
        return price * (1 - coupon.discount_value / 100);
    } else {
        return Math.max(0, price - coupon.discount_value);
    }
}
</script>