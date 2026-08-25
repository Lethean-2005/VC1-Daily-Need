<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Daily Needs</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&family=Kantumruy+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/fonts/tabler-icons.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Nunito', 'Kantumruy Pro', sans-serif; }
    body { min-height: 100vh; display: flex; overflow-x: hidden; }

    .auth-side {
      display: none;
      width: 50%;
      position: relative;
      overflow: hidden;
    }
    @media (min-width: 992px) { .auth-side { display: block; } }
    .auth-side img.bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .auth-side::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,.1) 60%, #fff 100%);
    }
    .auth-side-content { position: absolute; top: 40px; left: 40px; max-width: 320px; z-index: 1; }
    .auth-side h1 { font-size: 1.9rem; font-weight: 700; color: #1f2a1f; margin-bottom: 6px; }
    .auth-side p { color: #444; line-height: 1.5; }

    .auth-form-wrap { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 24px; }
    .auth-form { width: 100%; max-width: 400px; }

    .auth-tabs { display: flex; border-bottom: 1px solid #eee; margin-bottom: 32px; }
    .auth-tabs a {
      padding: 0 0 12px 0;
      margin-right: 28px;
      color: #999;
      font-weight: 600;
      text-decoration: none;
      border-bottom: 2px solid transparent;
    }
    .auth-tabs a.active { color: #1f2a1f; border-color: #3b82f6; }

    .auth-field { margin-bottom: 16px; position: relative; }
    .auth-field input {
      width: 100%;
      padding: 12px 14px;
      background: #f4f3ef;
      border: none;
      border-radius: 8px;
      font-size: .9rem;
      color: #1f2a1f;
    }
    .auth-field input:focus { outline: 2px solid #3b82f6; }
    .auth-field .toggle-eye {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #999;
      font-size: 1rem;
    }

    .auth-row { display: flex; align-items: center; justify-content: space-between; font-size: .85rem; margin: 4px 0 20px; }
    .auth-row a { color: #3b82f6; text-decoration: none; }
    .auth-row a:hover { text-decoration: underline; }

    .auth-submit {
      width: 100%;
      background: #3b82f6;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 12px;
      font-weight: 700;
      font-size: .95rem;
      cursor: pointer;
      transition: background .2s ease-in-out;
    }
    .auth-submit:hover { background: #2563eb; }

    .auth-switch { text-align: center; font-size: .9rem; color: #777; margin-top: 28px; }
    .auth-switch a { color: #3b82f6; font-weight: 600; text-decoration: none; }
    .auth-switch a:hover { text-decoration: underline; }

    .dn-error-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.45);
      z-index: 2000;
      align-items: center;
      justify-content: center;
    }
    .dn-error-overlay.show { display: flex; }
    .dn-error-card {
      background: #fff;
      border-radius: 14px;
      padding: 26px;
      max-width: 300px;
      width: 90%;
      text-align: center;
      box-shadow: 0 20px 50px rgba(0,0,0,.25);
    }
    .dn-error-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: #fdeceb;
      color: #e0455a;
      font-size: 1.3rem;
      margin-bottom: 12px;
    }
    .dn-error-card h5 { font-weight: 700; color: #1f2a1f; margin-bottom: 6px; font-size: 1rem; }
    .dn-error-card p { color: #777; font-size: .85rem; margin-bottom: 18px; }
    .dn-error-btn {
      padding: 9px 24px;
      border-radius: 8px;
      border: none;
      background: #3b82f6;
      color: #fff;
      font-weight: 600;
      font-size: .85rem;
      cursor: pointer;
    }
    .dn-error-btn:hover { background: #2f6bdb; }

    .auth-divider { display: flex; align-items: center; gap: 14px; margin: 22px 0; }
    .auth-divider .line { flex: 1; height: 1px; background: #eee; }
    .auth-divider span { font-size: .8rem; color: #999; }

    .auth-social { display: flex; flex-direction: column; gap: 10px; }
    .auth-social-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 11px;
      border-radius: 8px;
      border: 1px solid #eee;
      background: #fff;
      color: #333;
      font-weight: 600;
      font-size: .88rem;
      text-decoration: none;
      transition: background .2s ease-in-out;
    }
    .auth-social-btn:hover { background: #f7f7f5; color: #333; }
  </style>
</head>
<body>

  <div class="auth-side">
    <img src="/assets/images/auth-sidebar.webp" alt="" class="bg">
    <div class="auth-side-content">
      <h1>Welcome back</h1>
      <p>Log in to pick up where you left off &mdash; your cart, orders, and everyday essentials are waiting.</p>
    </div>
  </div>

  <div class="auth-form-wrap">
    <div class="auth-form">
      <div class="auth-tabs">
        <a href="/F_login" class="active">Login</a>
        <a href="/F_register">Register</a>
      </div>

      <form action="/authenticate" method="POST">
        <div class="auth-field">
          <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="auth-field">
          <input type="password" name="password" id="password" placeholder="Enter your password" required>
          <i class="ti ti-eye toggle-eye" id="togglePassword"></i>
        </div>
        <div class="auth-row">
          <label style="display:flex; align-items:center; gap:6px; color:#777;">
            <input type="checkbox" id="remember" style="width:auto;">
            Remember me
          </label>
          <a href="#">Forgot password?</a>
        </div>
        <button type="submit" class="auth-submit">Log In</button>
      </form>

      <div class="auth-divider"><div class="line"></div><span>OR</span><div class="line"></div></div>

      <div class="auth-social">
        <a href="/auth/google" class="auth-social-btn">
          <svg width="18" height="18" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47a5.53 5.53 0 01-2.4 3.63v3h3.88c2.27-2.09 3.54-5.17 3.54-8.87z" />
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3a7.4 7.4 0 01-4.05 1.14c-3.11 0-5.75-2.1-6.69-4.93H1.3v3.1A12 12 0 0012 24z" />
            <path fill="#FBBC05" d="M5.31 14.3a7.2 7.2 0 010-4.6v-3.1H1.3a12 12 0 000 10.8z" />
            <path fill="#EA4335" d="M12 4.75c1.76 0 3.34.61 4.59 1.8l3.44-3.44C17.94 1.19 15.24 0 12 0A12 12 0 001.3 6.6l4.01 3.1C6.25 6.86 8.89 4.75 12 4.75z" />
          </svg>
          Continue with Google
        </a>
      </div>

      <p class="auth-switch">Don't have an account? <a href="/F_register">Register</a></p>
    </div>
  </div>

  <script>
    const toggleBtn = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    toggleBtn.addEventListener('click', function () {
      const isHidden = passwordField.type === 'password';
      passwordField.type = isHidden ? 'text' : 'password';
      toggleBtn.classList.toggle('ti-eye', !isHidden);
      toggleBtn.classList.toggle('ti-eye-off', isHidden);
    });
  </script>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="dn-error-overlay show" id="loginErrorOverlay">
      <div class="dn-error-card">
        <i class="ti ti-alert-circle dn-error-icon"></i>
        <h5>Login Failed</h5>
        <p><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <button type="button" class="dn-error-btn" id="loginErrorClose">OK</button>
      </div>
    </div>
    <script>
      document.getElementById('loginErrorClose').addEventListener('click', function () {
        document.getElementById('loginErrorOverlay').classList.remove('show');
      });
    </script>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
</body>
</html>
