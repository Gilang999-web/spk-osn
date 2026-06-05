<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK OSN</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <img src="assets/images/logo_nesatma.jpg" alt="Logo SMP 1 Manonjaya" style="width: 80px; height: auto; margin-bottom: 15px; border-radius: 8px;">
            <div class="auth-brand" style="font-size: 1.5rem; line-height: 1.2;">Sistem Pendukung Keputusan</div>
            <div class="text-muted small mt-2">Penentuan Calon Peserta OSN</div>
        </div>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger py-2" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="?page=login" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label text-muted small fw-bold">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autofocus>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label text-muted small fw-bold">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        
        <div class="text-center mt-4">
            <small class="text-muted">&copy; 2026 SMP Negeri 1 Manonjaya</small>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
