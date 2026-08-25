<?php
require_once "Models/SocialAuthModel.php";
require_once "Models/UserModel.php";

class SocialAuthController extends BasecustomerController {

    private $auth;
    private $userModel;
    private $config;

    public function __construct() {
        $this->auth = new SocialAuthModel();
        $this->userModel = new UserModel();

        // On Railway, GOOGLE_CLIENT_ID/GOOGLE_CLIENT_SECRET come from env vars
        // (never baked into the image). Locally, fall back to Config/oauth.php.
        $configPath = __DIR__ . "/../../../Config/oauth.php";
        if (getenv('GOOGLE_CLIENT_ID')) {
            // Railway terminates TLS at the edge and forwards over plain HTTP,
            // so $_SERVER['HTTPS'] is never set here — check X-Forwarded-Proto.
            $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
            $scheme = ($forwardedProto === 'https' || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'] ?? 'localhost:8003';
            $this->config = [
                'google' => [
                    'client_id'     => getenv('GOOGLE_CLIENT_ID'),
                    'client_secret' => getenv('GOOGLE_CLIENT_SECRET'),
                    'redirect_uri'  => $scheme . '://' . $host . '/auth/google/callback',
                ],
            ];
        } else {
            $this->config = require $configPath;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ---------------------------------------------------------------
    // Google
    // ---------------------------------------------------------------

    public function googleRedirect() {
        $google = $this->config['google'];
        $state = bin2hex(random_bytes(16));
        $_SESSION['google_oauth_state'] = $state;

        $params = http_build_query([
            'client_id' => $google['client_id'],
            'redirect_uri' => $google['redirect_uri'],
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        $this->redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    public function googleCallback() {
        $google = $this->config['google'];

        if (empty($_GET['state']) || empty($_SESSION['google_oauth_state']) || $_GET['state'] !== $_SESSION['google_oauth_state']) {
            $_SESSION['error'] = "Login request could not be verified. Please try again.";
            $this->redirect('/F_login');
        }
        unset($_SESSION['google_oauth_state']);

        if (empty($_GET['code'])) {
            $_SESSION['error'] = "Google did not return an authorization code.";
            $this->redirect('/F_login');
        }

        // Exchange the authorization code for an access token
        $tokenResponse = $this->httpPost('https://oauth2.googleapis.com/token', [
            'code' => $_GET['code'],
            'client_id' => $google['client_id'],
            'client_secret' => $google['client_secret'],
            'redirect_uri' => $google['redirect_uri'],
            'grant_type' => 'authorization_code',
        ]);

        if (empty($tokenResponse['access_token'])) {
            $_SESSION['error'] = "Could not log in with Google. Please try again.";
            $this->redirect('/F_login');
        }

        // Fetch the profile that access token belongs to
        $profile = $this->httpGet('https://www.googleapis.com/oauth2/v3/userinfo', [
            'Authorization: Bearer ' . $tokenResponse['access_token'],
        ]);

        if (empty($profile['sub']) || empty($profile['email'])) {
            $_SESSION['error'] = "Google did not return enough profile information.";
            $this->redirect('/F_login');
        }

        $this->loginOrCreate([
            'provider_id' => $profile['sub'],
            'email' => $profile['email'],
            'name' => $profile['name'] ?? explode('@', $profile['email'])[0],
            'picture' => $profile['picture'] ?? '',
        ]);
    }

    // ---------------------------------------------------------------
    // Find-or-create / link / login logic
    // ---------------------------------------------------------------

    private function loginOrCreate($profile) {
        $existing = $this->auth->findByGoogleId($profile['provider_id']);

        if (!$existing) {
            // Not linked yet — see if an account with this email already exists and link to it.
            $existing = $this->auth->findByEmail($profile['email']);
            if ($existing) {
                $this->auth->linkGoogleId($existing['id'], $profile['provider_id']);
                // Backfill a profile picture if the account never had one
                $this->auth->fillProfilePictureIfEmpty($existing['id'], $profile['picture']);
                $existing = $this->auth->findByEmail($profile['email']);
            }
        }

        if (!$existing) {
            $this->auth->createFromGoogle($profile['name'], $profile['email'], $profile['provider_id'], $profile['picture']);
            $existing = $this->auth->findByEmail($profile['email']);
        }

        $_SESSION['user_name'] = $existing['username'];
        $_SESSION['user_id'] = $existing['id'];
        $_SESSION['user_role'] = $existing['role'];
        $_SESSION['user_email'] = $existing['email'];
        $_SESSION['user_phone'] = $existing['phone'];
        $_SESSION['user_profile'] = $existing['profile'];

        $this->userModel->updateLastLogin($existing['id']);

        $_SESSION['success'] = "Welcome, " . $existing['username'] . "!";
        $this->redirect('/');
    }

    // ---------------------------------------------------------------
    // Minimal HTTP helpers (no external HTTP client is installed)
    // ---------------------------------------------------------------

    private function httpPost($url, $fields) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true) ?: [];
    }

    private function httpGet($url, $headers = []) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true) ?: [];
    }
}
