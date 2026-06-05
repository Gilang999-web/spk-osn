<?php
require_once 'config/database.php';

try {
    $password_plain = 'admin123';
    $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);
    
    // Perbarui password untuk admin
    $stmt = $conn->prepare("UPDATE tb_users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$password_hash]);
    
    // Perbarui juga di file sql/spk_osn.sql
    $sql_file = 'sql/spk_osn.sql';
    if (file_exists($sql_file)) {
        $content = file_get_contents($sql_file);
        $content = preg_replace("/'\\$2y\\$10\\$[^']+'/", "'$password_hash'", $content);
        file_put_contents($sql_file, $content);
    }
    
    echo "<h1>Sukses!</h1>";
    echo "<p>Password admin telah di-reset ke: <strong>$password_plain</strong></p>";
    echo "<p><a href='index.php?page=login'>Klik di sini untuk kembali ke halaman Login</a></p>";
    
} catch (PDOException $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
