<footer class="dn-footer">
  <div class="container py-5">
    <div class="row g-4">
      <div class="col-lg-4 col-12">
        <img src="/assets/images/logo.png" alt="Daily Needs" class="dn-footer-logo">
        <p class="dn-footer-desc">Daily Needs brings your everyday groceries and household essentials together in one place &mdash; quality products, honest prices, delivered fast.</p>
        <div class="d-flex gap-2">
          <a href="#" aria-label="Twitter"><i class="ti ti-brand-twitter"></i></a>
          <a href="#" aria-label="Instagram"><i class="ti ti-brand-instagram"></i></a>
          <a href="#" aria-label="Facebook"><i class="ti ti-brand-facebook"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="ti ti-brand-linkedin"></i></a>
        </div>
      </div>

      <div class="col-lg-2 col-6">
        <h6 class="dn-footer-heading">Shop</h6>
        <ul class="list-unstyled">
          <li><a href="/">Home</a></li>
          <li><a href="/product">All Products</a></li>
          <li><a href="/about">About Us</a></li>
          <li><a href="/contact">Contact</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-6">
        <h6 class="dn-footer-heading">Categories</h6>
        <ul class="list-unstyled">
          <li><a href="/houeshold">Household</a></li>
          <li><a href="/beverage">Beverages</a></li>
          <li><a href="/snacks">Snacks</a></li>
          <li><a href="/cooking">Cooking</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-6">
        <h6 class="dn-footer-heading">Account</h6>
        <ul class="list-unstyled">
          <li><a href="/F_login">Login</a></li>
          <li><a href="/F_register">Register</a></li>
          <li><a href="/order_h">My Orders</a></li>
          <li><a href="/viewcart">View Cart</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-6">
        <h6 class="dn-footer-heading">Legal</h6>
        <ul class="list-unstyled">
          <li><a href="/privacy">Privacy</a></li>
          <li><a href="/terms">Terms</a></li>
          <li><a href="/security">Security</a></li>
          <li><a href="/cookies">Cookies</a></li>
        </ul>
      </div>
    </div>

    <hr class="dn-footer-divider">

    <div class="row align-items-center g-3">
      <div class="col-lg-6">
        <div class="d-flex flex-wrap gap-2">
          <span class="dn-footer-pill"><i class="ti ti-shield-check"></i> Secure Checkout</span>
          <span class="dn-footer-pill"><i class="ti ti-certificate"></i> Verified Sellers</span>
          <span class="dn-footer-pill"><i class="ti ti-star"></i> 4.8/5 Customer Rating</span>
        </div>
      </div>
      <div class="col-lg-6">
        <form class="d-flex gap-2 justify-content-lg-end">
          <input type="email" class="dn-footer-input" placeholder="Get product updates...">
          <button type="submit" class="dn-footer-subscribe">Subscribe</button>
        </form>
      </div>
    </div>

    <hr class="dn-footer-divider">

    <div class="row align-items-center">
      <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
        <p class="dn-footer-copy">&copy; 2026 Daily Needs. All rights reserved.</p>
      </div>
      <div class="col-md-6 text-center text-md-end">
        <div class="d-flex justify-content-center justify-content-md-end gap-4 flex-wrap">
          <a href="/status" class="dn-footer-legal">Status</a>
          <a href="/sitemap" class="dn-footer-legal">Sitemap</a>
          <a href="/accessibility" class="dn-footer-legal">Accessibility</a>
        </div>
      </div>
    </div>
  </div>
</footer>

<style>
  .dn-footer {
    background: #fff;
    border-top: 1px solid #eee;
    font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
  }
  .dn-footer-logo { height: 44px; width: auto; margin-bottom: 14px; }
  .dn-footer-desc { color: #777; font-size: .9rem; line-height: 1.6; max-width: 340px; margin-bottom: 16px; }
  .dn-footer .d-flex.gap-2 a {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    transition: background .2s ease-in-out, color .2s ease-in-out;
  }
  .dn-footer .d-flex.gap-2 a:hover { background: #1f2a1f; color: #fff; border-color: #1f2a1f; }
  .dn-footer-heading { font-weight: 700; font-size: .95rem; color: #1f2a1f; margin-bottom: 16px; }
  .dn-footer ul li { margin-bottom: 10px; }
  .dn-footer ul li a { color: #777; font-size: .9rem; text-decoration: none; transition: color .2s ease-in-out; }
  .dn-footer ul li a:hover { color: #1f2a1f; }
  .dn-footer-divider { border-color: #eee; margin: 8px 0 20px; }

  .dn-footer-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f4f3ef;
    color: #555;
    font-size: .82rem;
    font-weight: 600;
    padding: 8px 14px;
    border-radius: 5px;
  }
  .dn-footer-pill i { color: #d98c4a; }

  .dn-footer-input {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 8px 14px;
    font-size: .82rem;
    width: 220px;
    max-width: 100%;
  }
  .dn-footer-input:focus { outline: none; border-color: #d98c4a; }
  .dn-footer-subscribe {
    background: #d98c4a;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 8px 14px;
    font-weight: 600;
    font-size: .82rem;
    white-space: nowrap;
    transition: background .2s ease-in-out;
  }
  .dn-footer-subscribe:hover { background: #bf7638; }

  .dn-footer-copy { color: #999; font-size: .85rem; margin: 0; }
  .dn-footer-legal { color: #999; font-size: .85rem; text-decoration: none; }
  .dn-footer-legal:hover { color: #1f2a1f; text-decoration: underline; }
</style>
