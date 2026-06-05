<?php require_once 'views/layouts/header.php'; ?>

<?php
$mode       = $data['mode'] ?? 'create';
$alternatif = $data['alternatif'] ?? null;
$error      = $data['error'] ?? '';

$is_edit = ($mode === 'edit' && $alternatif);
$title   = $is_edit ? 'Edit Data Siswa' : 'Tambah Data Siswa';
?>

<!-- Toast Notification -->
<?php if (!empty($error)): ?>
<div class="toast-notification toast-error" id="autoToast">
    <div class="toast-icon">
        <i class="bi bi-exclamation-triangle-fill"></i>
    </div>
    <span><?= htmlspecialchars($error) ?></span>
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
        <h4 class="mb-1 fw-bold"><?= $title ?></h4>
        <p class="text-muted mb-0 small">
            <?= $is_edit ? 'Perbarui data siswa calon peserta OSN.' : 'Tambahkan siswa baru sebagai alternatif dalam perhitungan SPK.' ?>
        </p>
    </div>
    <a href="?page=alternatif" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Form Card -->
<div class="card border-0">
    <div class="card-body p-4">
        <form method="POST" action="?page=alternatif&action=<?= $is_edit ? 'update&id=' . $alternatif['id'] : 'store' ?>">
            <div class="row g-4">
                <!-- Nama Siswa -->
                <div class="col-md-6">
                    <label for="nama_siswa" class="form-label fw-semibold small">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_siswa" name="nama_siswa"
                           placeholder="Contoh: Ahmad Rizki Pratama"
                           value="<?= htmlspecialchars($alternatif['nama_siswa'] ?? '') ?>" required>
                    <div class="form-text">Nama lengkap siswa sesuai data sekolah</div>
                </div>

                <!-- Kelas -->
                <div class="col-md-3">
                    <label for="kelas" class="form-label fw-semibold small">Kelas <span class="text-danger">*</span></label>
                    <select class="form-select" id="kelas" name="kelas" required>
                        <option value="" disabled <?= empty($alternatif['kelas'] ?? '') ? 'selected' : '' ?>>-- Pilih Kelas --</option>
                        <option value="VII" <?= (($alternatif['kelas'] ?? '') === 'VII') ? 'selected' : '' ?>>Kelas VII</option>
                        <option value="VIII" <?= (($alternatif['kelas'] ?? '') === 'VIII') ? 'selected' : '' ?>>Kelas VIII</option>
                    </select>
                    <div class="form-text">Kelas siswa saat ini</div>
                </div>

                <!-- Bidang OSN -->
                <div class="col-md-3">
                    <label for="bidang_osn" class="form-label fw-semibold small">Bidang OSN <span class="text-danger">*</span></label>
                    <select class="form-select" id="bidang_osn" name="bidang_osn" required>
                        <option value="" disabled <?= empty($alternatif['bidang_osn'] ?? '') ? 'selected' : '' ?>>-- Pilih Bidang --</option>
                        <option value="IPA" <?= (($alternatif['bidang_osn'] ?? '') === 'IPA') ? 'selected' : '' ?>>
                            IPA
                        </option>
                        <option value="IPS" <?= (($alternatif['bidang_osn'] ?? '') === 'IPS') ? 'selected' : '' ?>>
                            IPS
                        </option>
                        <option value="Matematika" <?= (($alternatif['bidang_osn'] ?? '') === 'Matematika') ? 'selected' : '' ?>>
                            Matematika
                        </option>
                    </select>
                    <div class="form-text">Bidang OSN yang diikuti siswa</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-<?= $is_edit ? 'check-lg' : 'plus-circle' ?> me-1"></i>
                    <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Siswa' ?>
                </button>
                <a href="?page=alternatif" class="btn btn-light px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
