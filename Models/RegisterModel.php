<?php

class RegisterModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
        
    }

    // Check if email already exists in the database
    public function emailExists($email){
        $result = $this->db->query("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        return $result->rowCount() > 0;
    }

    // Register a new user in the database
    public function registerUser($username, $email, $phone, $password, $profile, $role) {
        // Check if email already exists
        if ($this->emailExists($email)) {
            return false; // Or you could throw an exception
        }
        // $role is validated by the caller (RegisterController::store()) before reaching here
        $result = $this->db->query("INSERT INTO users (username, email, phone, password, profile, role) VALUES (:username, :email, :phone, :password, :profile, :role)",[
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'profile' => $profile
            
        ]);
        return $result;

    }
}
