<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .register-container {
            width: 950px;
            display: flex;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .register-image {
            flex: 1;
            background: url('/assets/images/Daily.jpg') no-repeat center center/cover;
        }
        .register-form {
            flex: 1;
            padding: 40px;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .register-form h2 {
            font-weight: bold;
            color: #4b7043;
            text-align: center;
        }
        .form-group {
            position: relative;
            margin-bottom: 15px;
        }
        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6a9c5a;
        }
        .form-control, select {
            padding-left: 40px;
            height: 50px;
            border-radius: 10px;
            border: 1px solid #6a9c5a;
            width: 100%;
        }
        .btn-register {
            background: #6a9c5a;
            color: white;
            font-weight: bold;
            padding: 12px;
            border-radius: 10px;
            border: none;
        }
        .btn-register:hover {
            background: #567a48;
        }
    </style>
</head>
<body>

<div class="register-container">
    <!-- Left Side (Image) -->
    <div class="register-image"></div>

    <!-- Right Side (Form) -->
    <div class="register-form">
        <h2>CREATE AN ACCOUNT</h2>
        <form action="/register/store" method="POST" enctype="multipart/form-data">
            <!-- Username -->
            <div class="form-group">
                <i class="ti ti-user"></i>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>

            <!-- Email -->
            <div class="form-group">
                <i class="ti ti-mail"></i>
                <input type="email" class="form-control" name="email" placeholder="Email Address" required>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <i class="ti ti-phone"></i>
                <input type="text" class="form-control" name="phone" placeholder="Phone Number" required>
            </div>

            <!-- Password -->
            <div class="form-group">
                <i class="ti ti-lock"></i>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>

            <!-- Role Selection -->
            <div class="form-group">
                <i class="ti ti-users"></i>
                <select class="form-control" name="role" required>
                    <option selected value="users">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <!-- Profile Image -->
            <div class="form-group">
                <label for="imageURL">Profile Image</label>
                <input type="file" class="form-control" id="imageURL" name="profile">
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn btn-register w-100 mt-3">REGISTER</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
