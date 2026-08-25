<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /F_login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['currency'])) {
    $_SESSION['currency'] = $_POST['currency'];
}

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

$cartItems = isset($data['cartItems']) ? $data['cartItems'] : [];
$user = isset($data['user']) ? $data['user'] : null;
$locations = isset($data['locations']) ? $data['locations'] : [];
$error = isset($data['error']) ? $data['error'] : null;

// Get applied coupon from session
$applied_coupon = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;

// Calculate subtotal with discount
$subtotal = 0;
foreach ($cartItems as $item) {
    $price = floatval($item['price'] ?? 0);
    $quantity = intval($item['quantity'] ?? 1);
    $discounted_price = getDiscountedPrice($price, $applied_coupon);
    $subtotal += $discounted_price * $quantity;
}
?>

<style>
  .h-custom {
    background: rgb(238,174,202);
    background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(47,99,161,1) 100%);
  }

  .infor {
    background: rgb(238,174,202);
    background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(98,193,218,1) 100%);
    border-radius: 5px;
  }

  .car_d {
    height: 4rem;
    display: flex;
    align-items: center;
  }

  .Carr_d {
    border: 1px solid green;
    border-radius: 5px;
  }

  .image {
    width: 4rem;
    height: 4rem;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .img-fluid {
    width: 100%;
    height: auto;
    transition: transform 0.3s ease-out;
  }

  .img-fluid:hover {
    transform: scale(1.1);
  }

  .card-body {}

  .payment-methods {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .payment-option {
    cursor: pointer;
    padding: 5px;
    border-radius: 5px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .payment-option.selected {
    transform: scale(1.1);
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
  }

  a .fa-cc-mastercard {
    background: linear-gradient(90deg, #EB001B 50%, #FF5F00 50%);
  }

  a .fa-cc-visa {
    color: #142787;
  }

  a .fa-cc-amex {
    color: #0077A6;
  }

  a .scan-qr-icon {
    margin-top: 0px !important;
    width: 32px;
    height: 32px;
    border-radius: 5px;
  }

  #qr-code-payment {
    display: none;
    text-align: center;
    margin-top: 20px;
  }

  #qr-code-payment img {
    width: 200px;
    height: 200px;
    border: 2px solid #fff;
    border-radius: 10px;
  }

  #qr-code-payment p {
    margin-top: 10px;
    font-size: 14px;
    color: #fff;
  }

  .remove-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: background 0.3s ease;
    border-radius: 50%;
  }

  .remove-item:hover {
    background: red;
    color: white;
  }

  .remove-item:hover i {
    color: white;
  }

  .text-body {
    background: green;
    color: white !important;
    padding: 4px 12px;
    border-radius: 50px;
    display: inline-block;
    transition: transform 0.3s ease-out;
  }

  .text-body:hover {
    transform: translateX(-5px);
  }

  .num-it {
    background: pink !important;
    color: white !important;
    padding: 5px 10px !important;	
    border-radius: 100px !important;
    font-weight: bold !important;
    font-size: 15px !important;
  }

  #place-order-btn {
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  #place-order-btn:hover {
    background: green;
  }

  .original-price {
    text-decoration: line-through;
    color: #999;
    margin-right: 5px;
  }

  .discounted-price {
    color: #28a745;
    font-weight: bold;
  }
</style>

<section class="h-100 h-custom" style="background-color: #eee;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col">
        <div class="card">
          <div class="card-body p-4">
            <div class="row">
              <!-- Cart Items Section -->
              <div class="col-lg-7">
                <h5 class="mb-3"><a href="/product" class="text-body"><i class="ti ti-arrow-narrow-left me-2"></i>CONTINUE SHOPPING</a></h5>
                <hr>

                <div class="d-flex justify-content-between align-items-center mb-4">
                  <div>
                    <p class="mb-1">SHOPPING CART</p>
                    <p>YOU HAVE <b class="num-it"><?php echo count($cartItems); ?></b> PRODUCTS IN YOUR CART</p>
                  </div>
                </div>

                <?php if (empty($cartItems)): ?>
                  <div class="alert alert-warning" role="alert">
                    No items in your cart. <a href="/product" class="alert-link">Go back to products</a>.
                  </div>
                <?php else: ?>
                  <?php foreach ($cartItems as $item): ?>
                    <?php
                    $productId = htmlspecialchars($item['product_id'] ?? '');
                    $productName = htmlspecialchars($item['productname'] ?? 'Unknown Product');
                    $imageURL = htmlspecialchars($item['imageURL'] ?? 'https://via.placeholder.com/65');
                    $price = floatval($item['price'] ?? 0);
                    $quantity = intval($item['quantity'] ?? 1);
                    $discounted_price = getDiscountedPrice($price, $applied_coupon);
                    $itemSubtotal = $discounted_price * $quantity;
                    ?>
                    <div class="card mb-3" data-product-id="<?php echo $productId; ?>">
                      <div class="card-body Carr_d">
                        <div class="d-flex justify-content-between car_d">
                          <div class="d-flex flex-row align-items-center">
                            <div class="image">
                              <img src="<?php echo $imageURL; ?>" class="img-fluid rounded-3" alt="<?php echo $productName; ?>" style="width: 65px;">
                            </div>
                            <div class="ms-3">
                              <h5><?php echo $productName; ?></h5>
                              <p class="small mb-0"><?php echo htmlspecialchars($item['descriptions'] ?? 'N/A'); ?></p>
                            </div>
                          </div>
                          <div class="d-flex flex-row align-items-center">
                            <div style="width: 50px;">
                              <h5 class="fw-normal mb-0"><?php echo $quantity; ?></h5>
                            </div>
                            <div style="width: 80px;">
                              <h5 class="mb-0 product-price" data-price-usd="<?php echo $price; ?>" data-discounted-price-usd="<?php echo $discounted_price; ?>">
                                <?php
                                if ($applied_coupon && $discounted_price < $price) {
                                  echo "<span class='original-price'>$" . number_format($price * $quantity, 2) . "</span> ";
                                  echo "<span class='discounted-price'>$" . number_format($itemSubtotal, 2) . "</span>";
                                } else {
                                  echo "$" . number_format($itemSubtotal, 2);
                                }
                                ?>
                              </h5>
                            </div>
                            <a href="#" class="remove-item" data-product-id="<?php echo $productId; ?>" style="color: #cecece;"><i class="ti ti-trash-alt"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>

              <!-- Card Details Section -->
              <div class="col-lg-5">
                <div class="card bg-primary text-white rounded-3">
                  <div class="card-body infor">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                      <h5 class="mb-0" style="color:white;">CARD DETAILS</h5>
                      <?php if ($user && isset($user['profile'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile']); ?>" class="img-fluid rounded-3" style="width: 100px;" alt="<?php echo htmlspecialchars($user['username'] ?? 'User'); ?> Avatar">
                      <?php else: ?>
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-6.webp" class="img-fluid rounded-3" style="width: 45px;" alt="Default Avatar">
                      <?php endif; ?>
                    </div>

                    <?php if ($error): ?>
                      <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                      </div>
                    <?php elseif (!$user): ?>
                      <div class="alert alert-danger" role="alert">
                        User information not found. Please <a href="/F_login" class="alert-link">log in</a> again.
                        <br>
                        <small>Debug: Session user_id = <?php echo isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['user_id']) : 'Not set'; ?></small>
                      </div>
                    <?php else: ?>
                      <div class="mb-3">
                        <h6 class="text-white">USER INFORMATION</h6>
                        <p class="small mb-0"><strong>NAME:    </strong> <?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></p>
                        <p class="small mb-0"><strong>EMAIL:   </strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                        <p class="small mb-0"><strong>ADDRESS: </strong> <?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?></p>
                      </div>
                    <?php endif; ?>

                    <?php if ($applied_coupon): ?>
                      <div class="mb-3">
                        <p class="small mb-0 text-white">
                          <strong>Applied Coupon:</strong> <?php echo htmlspecialchars($applied_coupon['code']); ?> 
                          (<?php echo $applied_coupon['discount_type'] === 'percentage' ? $applied_coupon['discount_value'] . '%' : '$' . $applied_coupon['discount_value']; ?> off)
                        </p>
                      </div>
                    <?php endif; ?>

                    <form action="/checkout/process" method="POST" id="checkout-form">
                      <div id="cart-items-inputs">
                        <?php foreach ($cartItems as $item): ?>
                          <div class="cart-item-inputs" data-product-id="<?php echo htmlspecialchars($item['product_id']); ?>">
                            <input type="hidden" name="cart_items[<?php echo htmlspecialchars($item['product_id']); ?>][product_id]" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                            <input type="hidden" name="cart_items[<?php echo htmlspecialchars($item['product_id']); ?>][quantity]" value="<?php echo htmlspecialchars($item['quantity']); ?>">
                            <input type="hidden" name="cart_items[<?php echo htmlspecialchars($item['product_id']); ?>][price]" value="<?php echo htmlspecialchars($item['price']); ?>">
                            <input type="hidden" name="cart_items[<?php echo htmlspecialchars($item['product_id']); ?>][discounted_price]" value="<?php echo getDiscountedPrice(floatval($item['price']), $applied_coupon); ?>">
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <input type="hidden" id="total_price_input" name="total_price" value="<?php echo $subtotal; ?>">
                      <input type="hidden" id="currency_input" name="currency" value="USD">
                      <input type="hidden" id="payment_method_input" name="payment_method" value="Visa">

                      <p class="small mb-2">PAYMENT TYPE</p>
                      <div class="payment-methods">
                        <a href="#" class="payment-option text-white" data-method="Mastercard"><i class="ti ti-credit-card fa-2x"></i></a>
                        <a href="#" class="payment-option text-white selected" data-method="Visa"><i class="ti ti-credit-card fa-2x"></i></a>
                        <a href="#" class="payment-option text-white" data-method="Amex"><i class="ti ti-credit-card fa-2x"></i></a>
                        <a href="#" class="payment-option text-white" data-method="ScanQRCode">
                          <img src="/assets/images/QR.jpg" class="scan-qr-icon" alt="Scan QR Code">
                        </a>
                      </div>

                      <div class="mt-4" id="card-details">
                        <div data-mdb-input-init class="form-outline form-white mb-4">
                          <input type="text" id="typeName" name="card_holder_name" class="form-control form-control-lg" size="17" placeholder="Cardholder's Name" required />
                          <label class="form-label" for="typeName">Cardholder's Name</label>
                        </div>

                        <div data-mdb-input-init class="form-outline form-white mb-4">
                          <input type="text" id="typeText" name="card_number" class="form-control form-control-lg" size="17" placeholder="1234 5678 9012 3457" minlength="19" maxlength="19" required />
                          <label class="form-label" for="typeText">Card Number</label>
                        </div>

                        <div class="row mb-4">
                          <div class="col-md-6">
                            <div data-mdb-input-init class="form-outline form-white">
                              <input type="text" id="typeExp" name="expiry_date" class="form-control form-control-lg" placeholder="MM/YYYY" size="7" minlength="7" maxlength="7" required />
                              <label class="form-label" for="typeExp">Expiration</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div data-mdb-input-init class="form-outline form-white">
                              <input type="password" id="typeCvv" name="cvv" class="form-control form-control-lg" placeholder="●●●" size="1" minlength="3" maxlength="3" required />
                              <label class="form-label" for="typeCvv">Cvv</label>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div id="qr-code-payment">
                        <img src="/assets/images/QRkh.jpg" alt="QR Code for Payment">
                        <p>Scan this QR code to complete your payment.</p>
                      </div>

                      <div class="mb-3">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-control" id="currency" name="currency" onchange="updateTotalPrice()" required>
                          <option value="USD">USD</option>
                          <option value="KH Riel">KH Riel</option>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label for="location_id" class="form-label">Shipping Location</label>
                        <select class="form-control" id="location_id" name="location_id" required>
                          <?php if (empty($locations)): ?>
                            <option value="">No locations available</option>
                          <?php else: ?>
                            <?php foreach ($locations as $location): ?>
                              <option value="<?php echo htmlspecialchars($location['id']); ?>">
                                <?php echo htmlspecialchars($location['location_name']); ?>
                              </option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>

                      <hr class="my-4">

                      <div class="d-flex justify-content-between">
                        <p class="mb-2">Subtotal</p>
                        <p class="mb-2 subtotal">$<?php echo number_format($subtotal, 2); ?></p>
                      </div>

                      <div>
                        <button type="submit" class="btn btn-info w-100" id="place-order-btn">Place Order</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
const exchangeRate = 4000;
const appliedCoupon = <?php echo json_encode($applied_coupon); ?>;

function getDiscountedPrice(price, coupon) {
  if (!coupon) return price;
  if (coupon.discount_type === 'percentage') {
    return price * (1 - coupon.discount_value / 100);
  } else {
    return Math.max(0, price - coupon.discount_value);
  }
}

function updateTotalPrice() {
  const currency = document.getElementById('currency').value;
  let totalPriceUSD = 0;

  const cartItems = document.querySelectorAll('.card.mb-3[data-product-id]');
  cartItems.forEach(item => {
    const priceUSD = parseFloat(item.querySelector('.product-price').dataset.priceUsd) || 0;
    const discountedPriceUSD = parseFloat(item.querySelector('.product-price').dataset.discountedPriceUsd) || priceUSD;
    const quantity = parseInt(item.querySelector('.fw-normal').textContent) || 1;
    const itemSubtotalUSD = discountedPriceUSD * quantity;
    const productPriceElement = item.querySelector('.product-price');

    const formattedItemSubtotal = currency === 'KH Riel' 
      ? `${(itemSubtotalUSD * exchangeRate).toLocaleString()} KH Riel` 
      : (appliedCoupon && discountedPriceUSD < priceUSD 
          ? `<span class="original-price">$${ (priceUSD * quantity).toFixed(2)}</span> <span class="discounted-price">$${itemSubtotalUSD.toFixed(2)}</span>` 
          : `$${itemSubtotalUSD.toFixed(2)}`);

    productPriceElement.innerHTML = formattedItemSubtotal;
    totalPriceUSD += itemSubtotalUSD;
  });

  const shippingUSD = 20;
  const totalPriceWithShippingUSD = totalPriceUSD + shippingUSD;

  const totalPrice = currency === 'KH Riel' ? totalPriceUSD * exchangeRate : totalPriceUSD;
  const totalPriceWithShipping = currency === 'KH Riel' ? totalPriceWithShippingUSD * exchangeRate : totalPriceWithShippingUSD;
  const shippingDisplay = currency === 'KH Riel' ? `${(shippingUSD * exchangeRate).toLocaleString()} KH Riel` : `$${shippingUSD.toFixed(2)}`;

  document.querySelector('.subtotal').textContent = currency === 'KH Riel' 
    ? `${totalPrice.toLocaleString()} KH Riel` 
    : `$${totalPrice.toFixed(2)}`;

  document.getElementById('total_price_input').value = totalPriceWithShippingUSD.toFixed(2);
  document.getElementById('currency_input').value = currency;

  document.querySelector('.mb-0').textContent = `YOU HAVE ${cartItems.length} PRODUCTS IN YOUR CART`;

  const placeOrderBtn = document.getElementById('place-order-btn');
  placeOrderBtn.disabled = cartItems.length === 0;
}

function removeFromCart(productId) {
  fetch('/cart/remove', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ product_id: productId }),
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const itemElement = document.querySelector(`.card.mb-3[data-product-id="${productId}"]`);
      if (itemElement) itemElement.remove();

      const inputElement = document.querySelector(`.cart-item-inputs[data-product-id="${productId}"]`);
      if (inputElement) inputElement.remove();

      updateTotalPrice();

      const remainingItems = document.querySelectorAll('.card.mb-3[data-product-id]');
      if (remainingItems.length === 0) {
        const cartSection = document.querySelector('.col-lg-7');
        cartSection.innerHTML = `
          <h5 class="mb-3"><a href="/product" class="text-body"><i class="ti ti-arrow-narrow-left me-2"></i>Continue shopping</a></h5>
          <hr>
          <div class="alert alert-warning" role="alert">
            No items in your cart. <a href="/product" class="alert-link">Go back to products</a>.
          </div>
        `;
      }
    } else {
      alert('Failed to remove item: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error removing item:', error);
    alert('An error occurred while removing the item.');
  });
}

function updatePaymentMethod(method) {
  const paymentMethodInput = document.getElementById('payment_method_input');
  const cardDetails = document.getElementById('card-details');
  const qrCodePayment = document.getElementById('qr-code-payment');
  const paymentOptions = document.querySelectorAll('.payment-option');

  paymentMethodInput.value = method;

  paymentOptions.forEach(option => {
    option.classList.toggle('selected', option.getAttribute('data-method') === method);
  });

  if (method === 'ScanQRCode') {
    cardDetails.style.display = 'none';
    cardDetails.querySelectorAll('input').forEach(input => input.required = false);
    qrCodePayment.style.display = 'block';
  } else {
    cardDetails.style.display = 'block';
    cardDetails.querySelectorAll('input').forEach(input => input.required = true);
    qrCodePayment.style.display = 'none';
  }
}

document.querySelectorAll('.remove-item').forEach(button => {
  button.addEventListener('click', function(e) {
    e.preventDefault();
    const productId = this.getAttribute('data-product-id');
    removeFromCart(productId);
  });
});

document.querySelectorAll('.payment-option').forEach(option => {
  option.addEventListener('click', function(e) {
    e.preventDefault();
    const method = this.getAttribute('data-method');
    updatePaymentMethod(method);
  });
});

document.getElementById('checkout-form').addEventListener('submit', function(e) {
  e.preventDefault();

  const paymentMethod = document.getElementById('payment_method_input').value;

  if (paymentMethod !== 'ScanQRCode') {
    const cardNumber = document.getElementById('typeText').value;
    const expiryDate = document.getElementById('typeExp').value;
    const cvv = document.getElementById('typeCvv').value;

    if (!/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/.test(cardNumber)) {
      alert('Please enter a valid card number (e.g., 1234 5678 9012 3457)');
      return;
    }

    if (!/^(0[1-9]|1[0-2])\/\d{4}$/.test(expiryDate)) {
      alert('Please enter a valid expiry date (e.g., MM/YYYY)');
      return;
    }

    if (!/^\d{3}$/.test(cvv)) {
      alert('Please enter a valid CVV (3 digits)');
      return;
    }
  }

  const formData = new FormData(this);
  fetch('/checkout/process', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      window.location.href = '/confirmpayment?order_id=' + data.data.order_id;
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error submitting order:', error);
    alert('An error occurred while placing the order.');
  });
});

// Initialize UI
updateTotalPrice();
updatePaymentMethod('Visa');
</script>