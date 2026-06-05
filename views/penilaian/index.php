<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-0">Input Penilaian</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
            </ol>
        </nav>
    </div>
</div>

<?php
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
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

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="" method="GET" class="d-flex gap-3 align-items-end">
            <input type="hidden" name="page" value="penilaian">
            <div class="flex-grow-1">
                <label for="bidang_osn" class="form-label">Pilih Bidang OSN</label>
                <select name="bidang_osn" id="bidang_osn" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih Bidang OSN --</option>
                    <option value="IPA" <?= ($bidang_osn == 'IPA') ? 'selected' : '' ?>>Ilmu Pengetahuan Alam (IPA)</option>
                    <option value="IPS" <?= ($bidang_osn == 'IPS') ? 'selected' : '' ?>>Ilmu Pengetahuan Sosial (IPS)</option>
                    <option value="Matematika" <?= ($bidang_osn == 'Matematika') ? 'selected' : '' ?>>Matematika</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<?php if (!empty($bidang_osn)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-table me-2"></i> Form Penilaian - Bidang <?= htmlspecialchars($bidang_osn) ?>
        </h6>
    </div>
    <div class="card-body p-0">
        <?php if (empty($siswa)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p class="mb-0">Belum ada data siswa untuk bidang ini.</p>
            </div>
        <?php else: ?>
            <form action="?page=penilaian&action=store" method="POST">
                <input type="hidden" name="bidang_osn" value="<?= htmlspecialchars($bidang_osn) ?>">
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light text-nowrap">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Siswa</th>
                                <?php foreach ($kriteria as $k): ?>
                                    <th class="text-center" title="<?= htmlspecialchars($k['nama_kriteria']) ?>">
                                        <?= htmlspecialchars($k['kode']) ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($siswa as $s): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($s['nama_siswa']) ?></div>
                                        <div class="small text-muted">Kelas <?= htmlspecialchars($s['kelas']) ?></div>
                                    </td>
                                    <?php foreach ($kriteria as $k): ?>
                                        <?php 
                                            // Ambil nilai jika sudah ada
                                            $nilai_existing = isset($nilai_siswa[$s['id']][$k['id']]) ? $nilai_siswa[$s['id']][$k['id']] : ''; 
                                        ?>
                                        <td class="text-center">
                                            <input type="number" step="0.01" min="0" max="100" 
                                                   class="form-control form-control-sm text-center mx-auto" 
                                                   style="width: 80px;"
                                                   name="nilai[<?= $s['id'] ?>][<?= $k['id'] ?>]" 
                                                   value="<?= htmlspecialchars($nilai_existing) ?>"
                                                   placeholder="0.00"
                                                   required>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 bg-light text-end border-top">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Penilaian
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
