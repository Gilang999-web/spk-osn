<?php require_once 'views/layouts/header.php'; ?>

<?php
$user = $data['user'];
$success = $data['success'] ?? '';
$error = $data['error'] ?? '';

// Determine profile picture path
$foto_path = !empty($user['foto']) ? 'assets/images/uploads/' . htmlspecialchars($user['foto']) : null;
?>

<!-- Toast Notification -->
<?php if (!empty($success) || !empty($error)): ?>
<div class="toast-notification <?= !empty($success) ? 'toast-success' : 'toast-error' ?>" id="autoToast">
    <div class="toast-icon">
        <i class="bi <?= !empty($success) ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?>"></i>
    </div>
    <span><?= htmlspecialchars(!empty($success) ? $success : $error) ?></span>
</div>
<script>
    setTimeout(function() {
        var toast = document.getElementById('autoToast');
        if (toast) {
            toast.classList.add('toast-hide');
            setTimeout(function() { toast.remove(); }, 400);
        }
    }, 3000);
</script>
<?php endif; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Profil Admin</h4>
        <p class="text-muted mb-0 small">Kelola informasi profil dan keamanan akun Anda.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Form Update Profil -->
    <div class="col-lg-8">
        <div class="card border-0 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4"><i class="bi bi-person-lines-fill text-primary me-2"></i>Informasi Dasar</h6>
                <form action="?page=profil&action=update" method="POST" enctype="multipart/form-data">
                    <div class="row g-4 mb-4">
                        <!-- Foto Profil Preview & Upload -->
                        <div class="col-md-3 text-center">
                            <div class="position-relative d-inline-block mb-3">
                                <?php if ($foto_path && file_exists($foto_path)): ?>
                                    <img src="<?= $foto_path ?>" id="previewFoto" class="rounded-circle object-fit-cover border shadow-sm" style="width: 120px; height: 120px;" alt="Foto Profil">
                                <?php else: ?>
                                    <div id="previewInitials" class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center shadow-sm border" style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;">
                                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                                    </div>
                                    <img src="" id="previewFoto" class="rounded-circle object-fit-cover border shadow-sm d-none" style="width: 120px; height: 120px;" alt="Foto Profil">
                                <?php endif; ?>
                            </div>
                            <div class="small">
                                <label for="uploadFoto" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-camera me-1"></i> Pilih Foto
                                </label>
                                <input type="file" class="d-none" id="uploadFoto" name="foto" accept="image/jpeg, image/png, image/jpg" onchange="previewImage(event)">
                            </div>
                            <div class="form-text mt-2" style="font-size: 0.7rem;">Format: JPG/PNG.<br>Max 2MB.</div>
                        </div>
                        
                        <!-- Nama & Username -->
                        <div class="col-md-9">
                            <div class="mb-3">
                                <label for="nama" class="form-label fw-semibold small">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold small">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-end border-top pt-3">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Form Ubah Password -->
        <div class="card border-0">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4"><i class="bi bi-shield-lock-fill text-warning me-2"></i>Ubah Password</h6>
                <form action="?page=profil&action=update_password" method="POST">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="old_password" class="form-label fw-semibold small">Password Lama</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="old_password" name="old_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="old_password"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="new_password" class="form-label fw-semibold small">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="confirm_password" class="form-label fw-semibold small">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="text-end border-top pt-3 mt-4">
                        <button type="submit" class="btn btn-warning px-4"><i class="bi bi-key-fill me-1"></i> Perbarui Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Kolom Kanan: Info Akun -->
    <div class="col-lg-4">
        <div class="card border-0 bg-primary bg-opacity-10 text-primary mb-4">
            <div class="card-body p-4 text-center">
                <h6 class="fw-bold text-primary mb-1">Status Akun</h6>
                <div class="badge bg-primary text-white px-3 py-2 fs-6 rounded-pill mt-2">
                    <i class="bi bi-star-fill text-warning me-1"></i> <?= htmlspecialchars($user['role'] ?? 'Administrator') ?>
                </div>
            </div>
        </div>
        
        <div class="card border-0">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Informasi Sistem</h6>
                
                <div class="mb-3 pb-3 border-bottom">
                    <div class="text-muted small mb-1"><i class="bi bi-calendar-check me-1"></i> Terdaftar Sejak</div>
                    <div class="fw-medium">
                        <?php 
                        if (!empty($user['created_at'])) {
                            echo date('d F Y, H:i', strtotime($user['created_at']));
                        } else {
                            echo "-";
                        }
                        ?>
                    </div>
                </div>
                
                <div>
                    <div class="text-muted small mb-1"><i class="bi bi-clock-history me-1"></i> Login Terakhir</div>
                    <div class="fw-medium">
                        <?php 
                        if (!empty($user['last_login'])) {
                            echo date('d F Y, H:i', strtotime($user['last_login']));
                        } else {
                            echo "Belum ada data login";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Live Image Preview
function previewImage(event) {
    var input = event.target;
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('previewFoto');
            var initials = document.getElementById('previewInitials');
            
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (initials) initials.classList.add('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Show/Hide Password
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        var targetId = this.getAttribute('data-target');
        var input = document.getElementById(targetId);
        var icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>
