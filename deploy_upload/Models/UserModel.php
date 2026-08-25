<?php

class UserModel {
    private $db;

    // initial to start connect to the database
    public function __construct() {
        $this->db = Database::connect();
    }

    // Fetch all users from the database
    public function getUsers() {
        $result = $this->db->query("SELECT * FROM users");
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a single user by ID
    public function getUserById($id) {
        $result = $this->db->query("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Add user to the database
    public function addUser($username, $email, $phone, $password, $profile, $role){
        $result = $this->db->query("INSERT INTO users (username, email, phone, password, profile, role) VALUES (:username, :email, :phone, :password, :profile, :role)",[
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'profile' => $profile,
            'role' => $role
        ]);
        return $result;
    }

    // Update user in the database
    public function updateUser($id, $username, $email, $phone, $profile, $role){
        $result = $this->db->query("UPDATE users SET username = :username, email = :email, phone = :phone, profile = :profile, role = :role WHERE id = :id", [
            'id' => $id,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'profile' => $profile,
            'role' => $role
        ]);
        return $result;
    }

    // Delete user and all dependencies
    public function deleteUser($id)
    {
        // step 1: delete
        $user = $this->getUserById($id);
        if (!$user) {
            return false;
        }
        // step 2: delete
        $this->db->query("INSERT INTO trash_user (username, email, phone, password, profile, role) VALUES (:username, :email, :phone, :password, :profile, :role)", [
            'username' => $user['username'],
            'email' => $user['email'],
            'phone' => $user['phone'],
        'password' => $user['password'],
        'profile' => $user['profile'],
        'role' => $user['role']
    ]);
    //step 3: delete
    $this->db->query("DELETE FROM notifications WHERE user_id = :id", ['id' => $id]);
    $this->db->query("DELETE FROM payment_methods WHERE user_id = :id", ['id' => $id]);
    $this->db->query("DELETE FROM reviews WHERE user_id = :id", ['id' => $id]);
    $this->db->query("DELETE FROM shoppingcarts WHERE user_id = :id", ['id' => $id]);

    // step 4: delete
    $this->db->query("DELETE FROM transactions WHERE order_id IN (SELECT id FROM orders WHERE user_id = :id)", ['id' => $id]);
    $this->db->query("DELETE FROM orderitems WHERE order_id IN (SELECT id FROM orders WHERE user_id = :id)", ['id' => $id]);
    $this->db->query("DELETE FROM orders WHERE user_id = :id", ['id' => $id]);

    // step 5: delete
    $result = $this->db->query("DELETE FROM users WHERE id = :id", ['id' => $id]);
        return $result;
    } 


    // Fetch all users from trash_user
    public function getTrashUsers() {
        $result = $this->db->query("SELECT * FROM trash_user");
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    //Permanently delete user from trash_user
    public function permanentlyDeleteUser($id) {
        $result = $this->db->query("DELETE FROM trash_user WHERE id = :id", ['id' => $id]);
        return $result;
    }

    //Restore user from trash_user to users table
    public function restoreUser($id) {
        //step 1:restore from trash_user
        $user = $this->db->query("SELECT * FROM trash_user WHERE id = :id", ['id' => $id])->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        //step 2: restore Insert the user back into the `users` table
        $result = $this->db->query("INSERT INTO users (username, email, phone, password, profile, role) VALUES (:username, :email, :phone, :password, :profile, :role)", [
            'username' => $user['username'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'password' => $user['password'],
            'profile' => $user['profile'],
            'role' => $user['role']
        ]);
        //step 3: delete from trash_user
        if ($result) {
            $this->db->query("DELETE FROM trash_user WHERE id = :id", ['id' => $id]);
        }

        return $result;

    }
    
    //Update last_login timestamp when a user logs in
    public function updateLastLogin($id) {
        $result = $this->db->query("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id", [
            'id' => $id
        ]);
        return $result;
    }
    
    // Fetch all users with their active/inactive status
    public function getUsersWithStatus() {
        $result = $this->db->query("
            SELECT 
                id, 
                username, 
                email, 
                phone,
                profile, 
                role, 
                last_login, 
                CASE 
                    WHEN last_login IS NULL THEN 'Inactive' -- Handle NULL last_login
                    WHEN last_login >= NOW() - INTERVAL 5 MINUTE THEN 'Active' 
                    ELSE 'Inactive' 
                END AS status 
            FROM users
        ");
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

}