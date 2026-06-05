<?php
class UserModel {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, password, nama, foto, role FROM tb_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, username, nama, foto, role, created_at, last_login FROM tb_users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateLastLogin($id) {
        $stmt = $this->conn->prepare("UPDATE tb_users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function updateProfile($id, $nama, $username, $foto = null) {
        if ($foto) {
            $stmt = $this->conn->prepare("UPDATE tb_users SET nama = ?, username = ?, foto = ? WHERE id = ?");
            return $stmt->execute([$nama, $username, $foto, $id]);
        } else {
            $stmt = $this->conn->prepare("UPDATE tb_users SET nama = ?, username = ? WHERE id = ?");
            return $stmt->execute([$nama, $username, $id]);
        }
    }
    
    public function updatePassword($id, $new_password_hashed) {
        $stmt = $this->conn->prepare("UPDATE tb_users SET password = ? WHERE id = ?");
        return $stmt->execute([$new_password_hashed, $id]);
    }
}
?>
