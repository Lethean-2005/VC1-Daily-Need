<?php

class SocialAuthModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findByEmail($email) {
        $result = $this->db->query("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function findByGoogleId($googleId) {
        $result = $this->db->query("SELECT * FROM users WHERE google_id = :id", ['id' => $googleId]);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function linkGoogleId($userId, $googleId) {
        $this->db->query("UPDATE users SET google_id = :gid WHERE id = :id", [
            'gid' => $googleId,
            'id' => $userId,
        ]);
    }

    public function fillProfilePictureIfEmpty($userId, $picture) {
        if (empty($picture)) return;
        $this->db->query(
            "UPDATE users SET profile = :profile WHERE id = :id AND (profile IS NULL OR profile = '')",
            ['profile' => $picture, 'id' => $userId]
        );
    }

    public function createFromGoogle($username, $email, $googleId, $profile) {
        $this->db->query(
            "INSERT INTO users (username, email, phone, password, profile, role, google_id)
             VALUES (:username, :email, '', NULL, :profile, 'users', :google_id)",
            [
                'username' => $username,
                'email' => $email,
                'profile' => $profile,
                'google_id' => $googleId,
            ]
        );
        return $this->db->lastInsertId();
    }
}
