<?php
require_once 'models/UserModel.php';

class ProfilController {
    private $userModel;
    
    public function __construct($conn) {
        $this->userModel = new UserModel($conn);
    }
    
    public function index() {
        $id = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($id);
        
        $data = [
            'user' => $user,
            'success' => $_SESSION['flash_success'] ?? '',
            'error' => $_SESSION['flash_error'] ?? ''
        ];
        
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        require_once 'views/profil/index.php';
    }
    
    public function update() {
        $id = $_SESSION['user_id'];
        $nama = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        
        if (empty($nama) || empty($username)) {
            $_SESSION['flash_error'] = 'Nama dan Username tidak boleh kosong.';
            header('Location: ?page=profil');
            exit;
        }
        
        $foto_name = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png'];
            
            if (in_array($ext, $allowed_ext)) {
                $foto_name = 'user_' . $id . '_' . time() . '.' . $ext;
                $upload_dir = 'assets/images/uploads/';
                if (!move_uploaded_file($tmp_name, $upload_dir . $foto_name)) {
                    $_SESSION['flash_error'] = 'Gagal mengupload foto profil.';
                    header('Location: ?page=profil');
                    exit;
                }
            } else {
                $_SESSION['flash_error'] = 'Format foto harus JPG, JPEG, atau PNG.';
                header('Location: ?page=profil');
                exit;
            }
        }
        
        // Cek update query
        if ($this->userModel->updateProfile($id, $nama, $username, $foto_name)) {
            $_SESSION['nama'] = $nama;
            $_SESSION['username'] = $username;
            if ($foto_name) {
                $_SESSION['foto'] = $foto_name;
            }
            $_SESSION['flash_success'] = 'Profil berhasil diperbarui.';
        } else {
            $_SESSION['flash_error'] = 'Terjadi kesalahan saat memperbarui profil.';
        }
        
        header('Location: ?page=profil');
        exit;
    }
    
    public function update_password() {
        $id = $_SESSION['user_id'];
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['flash_error'] = 'Semua field password harus diisi.';
            header('Location: ?page=profil');
            exit;
        }
        
        if ($new_password !== $confirm_password) {
            $_SESSION['flash_error'] = 'Konfirmasi password baru tidak cocok.';
            header('Location: ?page=profil');
            exit;
        }
        
        // Verifikasi password lama
        // Kita butuh query spesifik untuk get password karena getUserById tidak return password
        global $conn;
        $stmt = $conn->prepare("SELECT password FROM tb_users WHERE id = ?");
        $stmt->execute([$id]);
        $current_pass = $stmt->fetchColumn();
        
        if (!password_verify($old_password, $current_pass)) {
            $_SESSION['flash_error'] = 'Password lama salah.';
            header('Location: ?page=profil');
            exit;
        }
        
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        if ($this->userModel->updatePassword($id, $hashed_password)) {
            $_SESSION['flash_success'] = 'Password berhasil diubah.';
        } else {
            $_SESSION['flash_error'] = 'Gagal mengubah password.';
        }
        
        header('Location: ?page=profil');
        exit;
    }
}
?>
