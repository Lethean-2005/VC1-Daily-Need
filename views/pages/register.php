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
  <title>Register - Daily Needs</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&family=Kantumruy+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/fonts/tabler-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    .auth-profile { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; }
    .auth-profile-preview {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      object-fit: cover;
      background: #f4f3ef;
      display: none;
    }
    .auth-profile-btn {
      background: #f4f3ef;
      color: #555;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: .85rem;
      font-weight: 600;
      cursor: pointer;
    }

    .auth-terms { display: flex; align-items: flex-start; gap: 8px; font-size: .82rem; color: #777; margin: 4px 0 20px; }
    .auth-terms a { color: #3b82f6; text-decoration: none; }
    .auth-terms a:hover { text-decoration: underline; }
    .auth-terms input { width: auto; margin-top: 3px; }

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
  </style>
</head>
<body>

  <div class="auth-side">
    <img src="/assets/images/auth-sidebar.webp" alt="" class="bg">
    <div class="auth-side-content">
      <h1>Create your account</h1>
      <p>Join Daily Needs to track orders, save favorites, and check out faster every time.</p>
    </div>
  </div>

  <div class="auth-form-wrap">
    <div class="auth-form">
      <div class="auth-tabs">
        <a href="/F_login">Login</a>
        <a href="/F_register" class="active">Register</a>
      </div>

      <form action="/register/store" method="POST" enctype="multipart/form-data">
        <div class="auth-field">
          <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="auth-field">
          <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="auth-field">
          <input type="tel" name="phone" placeholder="Phone number" required>
        </div>
        <div class="auth-field">
          <input type="password" name="password" id="password" placeholder="Password" required>
          <i class="ti ti-eye toggle-eye" id="togglePassword"></i>
        </div>

        <input type="hidden" name="role" value="users">

        <div class="auth-profile">
          <img id="preview" class="auth-profile-preview" src="#" alt="Profile preview">
          <input type="file" id="profile" name="profile" accept="image/*" onchange="previewImage(event)" hidden>
          <label for="profile" class="auth-profile-btn">Choose profile picture</label>
        </div>

        <label class="auth-terms">
          <input type="checkbox" required>
          <span>I agree to the <a href="/terms">Terms of Service</a> and <a href="/privacy">Privacy Policy</a></span>
        </label>

        <button type="submit" class="auth-submit">Create Account</button>
      </form>

      <p class="auth-switch">Already have an account? <a href="/F_login">Log In</a></p>
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

    function previewImage(event) {
      const preview = document.getElementById('preview');
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function () {
          preview.src = reader.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    }
  </script>

  <?php if (isset($_SESSION['error'])): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Registration Failed',
        text: "<?php echo addslashes($_SESSION['error']); ?>",
        confirmButtonColor: '#3b82f6'
      });
    </script>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Registration Successful!',
        text: 'You have successfully registered.',
        confirmButtonColor: '#3b82f6',
        confirmButtonText: 'OK'
      }).then(() => {
        window.location.href = '/F_login';
      });
    </script>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>
</body>
</html>
