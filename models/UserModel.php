<?php
class UserModel {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, password, nama FROM tb_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, username, nama FROM tb_users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
