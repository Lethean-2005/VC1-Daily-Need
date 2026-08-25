<?php
require_once "Models/LoginModel.php";
require_once "Models/UserModel.php";
class LoginController extends BaseAdminController {
    private $users;
    private $userModel;

    public function __construct(){
        $this->users = new LoginModel();
        $this->userModel = new UserModel();
    }

    public function login_admin(){ // Fixed typo
        require_once __DIR__. '/../../../views/admin/inventory/login.php';
    }

    public function login(){
        require_once __DIR__ . '/../../../views/pages/login.php';
    }

    public function authenticate(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $email = htmlspecialchars($_POST['email'] ?? '');
        $password = htmlspecialchars($_POST['password'] ?? '');
        $user = $this->users->getUserByEmail($email);

        if (!$user) {
            $_SESSION['error'] = "Email not found";
            $this->redirect("/F_login");
            return;
        }
        if (!password_verify($password, $user['password'])) {
            $_SESSION['error'] = "Incorrect password or email address";
            $this->redirect("/F_login");    
            return;
        }

        // Set session variables for all users
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['user_profile'] = $user['profile'];

        $this->userModel->updateLastLogin($user['id']);

        // Redirect based on role
        if ($user['role'] === 'admin') {
            $_SESSION['success'] = "Welcome, Admin " . $user['username'] . "!";
            $this->redirect("/");
        } elseif ($user['role'] === 'users') {
            $_SESSION['success'] = "Welcome, " . $user['username'] . "!";
            $this->redirect("/");
        } else {
            $_SESSION['error'] = "Invalid role: " . $user['role'];
            $this->redirect("/F_login");
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $redirectTo = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' ? '/admin-login' : '/login';
        session_destroy();
        $this->redirect("/F_login");
    }
}
?>