<?php require_once 'views/layouts/header.php'; ?>

<?php
$kriteria_list = $data['kriteria'] ?? [];
$success       = $data['success'] ?? '';
$error         = $data['error'] ?? '';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Data Kriteria</h4>
        <p class="text-muted mb-0 small">Kelola 5 kriteria penilaian (C1 – C5) yang digunakan dalam perhitungan AHP-SAW.</p>
    </div>
    <a href="?page=kriteria&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Kriteria
    </a>
</div>

<!-- Toast Notification -->
<?php if (!empty($success) || !empty($error)): ?>
<div class="toast-notification <?= !empty($success) ? 'toast-success' : 'toast-error' ?>" id="autoToast">
    <div class="toast-icon">
        <i class="bi <?= !empty($success) ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?>"></i>
    </div>
    <span><?= htmlspecialchars(!empty($success) ? $success : $error) ?></span>
</div>
<script>
    // Auto-dismiss after 3 seconds
    setTimeout(function() {
        var toast = document.getElementById('autoToast');
        if (toast) {
            toast.classList.add('toast-hide');
            setTimeout(function() { toast.remove(); }, 400);
        }
    }, 3000);
</script>
<?php endif; ?>

<!-- Info Alert -->
<div class="alert border-0 bg-primary bg-opacity-10 text-primary d-flex align-items-center py-2 mb-4" role="alert">
    <i class="bi bi-info-circle-fill me-2"></i>
    <span class="small">Kolom <strong>Bobot</strong> diisi otomatis oleh sistem setelah proses perhitungan AHP. Anda tidak perlu mengisi bobot secara manual.</span>
</div>

<!-- Kriteria Table -->
<div class="card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 60px;">No</th>
                        <th style="width: 100px;">Kode</th>
                        <th>Nama Kriteria</th>
                        <th style="width: 120px;">Jenis</th>
                        <th style="width: 150px;" class="text-center">Bobot (AHP)</th>
                        <th style="width: 130px;" class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($kriteria_list) > 0): ?>
                        <?php foreach ($kriteria_list as $i => $k): ?>
                        <tr>
                            <td class="ps-4 text-muted"><?= $i + 1 ?></td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-2">
                                    <?= htmlspecialchars($k['kode']) ?>
                                </span>
                            </td>
                            <td class="fw-medium"><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                            <td>
                                <?php if ($k['jenis'] == 'Benefit'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-arrow-up-short"></i> Benefit</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-arrow-down-short"></i> Cost</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($k['bobot'] !== null && $k['bobot'] > 0): ?>
                                    <span class="fw-bold text-primary"><?= number_format($k['bobot'], 4) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">— belum dihitung —</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="?page=kriteria&action=edit&id=<?= $k['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                        onclick="confirmDelete(<?= $k['id'] ?>, '<?= htmlspecialchars($k['kode']) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data kriteria.
                                <a href="?page=kriteria&action=create" class="text-primary">Tambah sekarang</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3"><i class="bi bi-exclamation-triangle-fill fs-1"></i></div>
                <h6 class="fw-bold mb-2">Hapus Kriteria?</h6>
                <p class="text-muted small mb-3">Kriteria <strong id="deleteKode"></strong> akan dihapus secara permanen.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-sm btn-danger px-4">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, kode) {
    document.getElementById('deleteKode').textContent = kode;
    document.getElementById('deleteForm').action = '?page=kriteria&action=delete&id=' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
