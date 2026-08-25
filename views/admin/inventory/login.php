<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            background: #d9ead3;
        }
        .login-container {
            width: 850px;
            display: flex;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .login-image {
            flex: 1;
            background: url('/assets/images/Daily.jpg') no-repeat center center/cover;
        }
        .login-form {
            flex: 1;
            padding: 40px;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-form h2 {
            font-weight: bold;
            color: #4b7043;
            text-align: center;
        }
        .form-group {
            position: relative;
        }
        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6a9c5a;
        }
        .form-control {
            padding-left: 40px;
            height: 50px;
            border-radius: 10px;
            border: 1px solid #6a9c5a;
        }
        .btn-login {
            background: #6a9c5a;
            color: white;
            font-weight: bold;
            padding: 12px;
            border-radius: 10px;
            border: none;
        }
        .btn-login:hover {
            background: #567a48;
        }
        .forgot-password {
            font-size: 14px;
            text-align: right;
            display: block;
            color: #4b7043;
            text-decoration: none;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Left Side (Image) -->
    <div class="login-image"></div>

    <!-- Right Side (Form) -->
    <div class="login-form">
        <h2>Welcome to Daily Needs</h2>
        <form action="/authenticate" method="POST">
            <!-- Email Input -->
            <div class="form-group mb-3">
                <i class="ti ti-mail"></i>
                <input type="email" class="form-control" id="email" name="email" placeholder="Admin Email" required>
            </div>

            <!-- Password Input -->
            <div class="form-group mb-3">
                <i class="ti ti-lock"></i>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>

            <!-- Forgot Password -->
            <a href="/forgot-password" class="forgot-password">Forgot Password?</a>

            <!-- Login Button -->
            <button type="submit" class="btn btn-login w-100 mt-3">LOGIN</button>
             <!-- Register Link -->
            <p class="text-center mt-3">
                Don't have an account? <a href="/register">Register here</a>
            </p>
            <p class="text-center mt-3">
                Not an admin? <a href="/user-login">User Login</a>
            </p>
        </form>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Show error message if exists -->
<?php if (isset($_SESSION['error'])): ?>
    <script>
        Swal.fire({
            icon: "error",
            title: "Admin Login Failed",
            text: "<?php echo $_SESSION['error']; ?>",
            confirmButtonColor: "#6a9c5a"
        });
    </script>
    <?php unset($_SESSION['error']); // Clear error after showing ?>
<?php endif; ?> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
