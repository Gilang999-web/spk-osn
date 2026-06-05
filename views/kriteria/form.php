<?php require_once 'views/layouts/header.php'; ?>

<?php
$mode     = $data['mode'] ?? 'create';
$kriteria = $data['kriteria'] ?? null;
$error    = $data['error'] ?? '';

$is_edit  = ($mode === 'edit' && $kriteria);
$title    = $is_edit ? 'Edit Kriteria' : 'Tambah Kriteria';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><?= $title ?></h4>
        <p class="text-muted mb-0 small">
            <?= $is_edit ? 'Perbarui data kriteria penilaian.' : 'Tambah kriteria penilaian baru ke dalam sistem.' ?>
        </p>
    </div>
    <a href="?page=kriteria" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

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

<!-- Form Card -->
<div class="card border-0">
    <div class="card-body p-4">
        <form method="POST" action="?page=kriteria&action=<?= $is_edit ? 'update&id=' . $kriteria['id'] : 'store' ?>">
            <div class="row g-4">
                <!-- Kode Kriteria -->
                <div class="col-md-4">
                    <label for="kode" class="form-label fw-semibold small">Kode Kriteria <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="kode" name="kode" 
                           placeholder="Contoh: C1" maxlength="5"
                           value="<?= htmlspecialchars($kriteria['kode'] ?? '') ?>" required>
                    <div class="form-text">Kode unik untuk kriteria (misal: C1, C2, dst)</div>
                </div>

                <!-- Nama Kriteria -->
                <div class="col-md-4">
                    <label for="nama_kriteria" class="form-label fw-semibold small">Nama Kriteria <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_kriteria" name="nama_kriteria"
                           placeholder="Contoh: Nilai Tes Seleksi"
                           value="<?= htmlspecialchars($kriteria['nama_kriteria'] ?? '') ?>" required>
                    <div class="form-text">Nama lengkap kriteria penilaian</div>
                </div>

                <!-- Jenis Kriteria -->
                <div class="col-md-4">
                    <label for="jenis" class="form-label fw-semibold small">Jenis Kriteria <span class="text-danger">*</span></label>
                    <select class="form-select" id="jenis" name="jenis" required>
                        <option value="Benefit" <?= (($kriteria['jenis'] ?? 'Benefit') == 'Benefit') ? 'selected' : '' ?>>
                            Benefit (Semakin tinggi semakin baik)
                        </option>
                        <option value="Cost" <?= (($kriteria['jenis'] ?? '') == 'Cost') ? 'selected' : '' ?>>
                            Cost (Semakin rendah semakin baik)
                        </option>
                    </select>
                    <div class="form-text">Menentukan arah normalisasi pada SAW</div>
                </div>

                <!-- Bobot (readonly) -->
                <?php if ($is_edit): ?>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Bobot (AHP)</label>
                    <input type="text" class="form-control" disabled readonly
                           value="<?= ($kriteria['bobot'] !== null && $kriteria['bobot'] > 0) ? number_format($kriteria['bobot'], 6) : '— Belum dihitung —' ?>">
                    <div class="form-text">Bobot diisi otomatis setelah perhitungan AHP</div>
                </div>
                <?php endif; ?>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-<?= $is_edit ? 'check-lg' : 'plus-circle' ?> me-1"></i>
                    <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Kriteria' ?>
                </button>
                <a href="?page=kriteria" class="btn btn-light px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
