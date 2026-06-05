<?php
require_once 'models/UserModel.php';

class AuthController {
    private $userModel;
    
    public function __construct($conn) {
        $this->userModel = new UserModel($conn);
    }
    
    public function login() {
        // Jika sudah login, redirect ke dashboard
        if (isset($_SESSION['user_id'])) {
            header("Location: ?page=dashboard");
            exit;
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error = 'Username dan password harus diisi!';
            } else {
                $user = $this->userModel->login($username, $password);
                
                if ($user) {
                    // Set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['username'] = $user['username'];
                    
                    header("Location: ?page=dashboard");
                    exit;
                } else {
                    $error = 'Username atau password salah!';
                }
            }
        }
        
        // Tampilkan view login
        require_once 'views/login.php';
    }
    
    public function logout() {
        // Hapus semua session
        session_unset();
        session_destroy();
        
        header("Location: ?page=login");
        exit;
    }
}
?>
