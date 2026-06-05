<?php require_once 'views/layouts/header.php'; ?>

<?php
$alternatif_list = $data['alternatif'] ?? [];
$filter_bidang   = $data['filter_bidang'] ?? '';
$count_all       = $data['count_all'] ?? 0;
$count_ipa       = $data['count_ipa'] ?? 0;
$count_ips       = $data['count_ips'] ?? 0;
$count_mtk       = $data['count_mtk'] ?? 0;
$success         = $data['success'] ?? '';
$error           = $data['error'] ?? '';
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
        <h4 class="mb-1 fw-bold">Data Alternatif (Siswa)</h4>
        <p class="text-muted mb-0 small">Kelola data siswa calon peserta OSN per bidang lomba.</p>
    </div>
    <a href="?page=alternatif&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
    </a>
</div>

<!-- Filter Tabs -->
<div class="card border-0 mb-4">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted small fw-semibold me-1"><i class="bi bi-funnel me-1"></i>Filter:</span>
            <a href="?page=alternatif" 
               class="btn btn-sm <?= empty($filter_bidang) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                Semua <span class="badge bg-white text-dark ms-1"><?= $count_all ?></span>
            </a>
            <a href="?page=alternatif&bidang=IPA"
               class="btn btn-sm <?= ($filter_bidang === 'IPA') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                <i class="bi bi-flask me-1"></i> IPA <span class="badge <?= ($filter_bidang === 'IPA') ? 'bg-white text-primary' : 'bg-secondary bg-opacity-10 text-secondary' ?> ms-1"><?= $count_ipa ?></span>
            </a>
            <a href="?page=alternatif&bidang=IPS"
               class="btn btn-sm <?= ($filter_bidang === 'IPS') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                <i class="bi bi-globe-americas me-1"></i> IPS <span class="badge <?= ($filter_bidang === 'IPS') ? 'bg-white text-primary' : 'bg-secondary bg-opacity-10 text-secondary' ?> ms-1"><?= $count_ips ?></span>
            </a>
            <a href="?page=alternatif&bidang=Matematika"
               class="btn btn-sm <?= ($filter_bidang === 'Matematika') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                <i class="bi bi-calculator me-1"></i> Matematika <span class="badge <?= ($filter_bidang === 'Matematika') ? 'bg-white text-primary' : 'bg-secondary bg-opacity-10 text-secondary' ?> ms-1"><?= $count_mtk ?></span>
            </a>
        </div>
    </div>
</div>

<!-- Siswa Table -->
<div class="card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 60px;">No</th>
                        <th>Nama Siswa</th>
                        <th style="width: 100px;">Kelas</th>
                        <th style="width: 160px;">Bidang OSN</th>
                        <th style="width: 130px;" class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($alternatif_list) > 0): ?>
                        <?php foreach ($alternatif_list as $i => $alt): ?>
                        <tr>
                            <td class="ps-4 text-muted"><?= $i + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle avatar-<?= strtolower($alt['bidang_osn'] === 'Matematika' ? 'mtk' : strtolower($alt['bidang_osn'])) ?>">
                                        <?= strtoupper(substr($alt['nama_siswa'], 0, 1)) ?>
                                    </div>
                                    <span class="fw-medium"><?= htmlspecialchars($alt['nama_siswa']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold px-3 py-2">
                                    Kelas <?= htmlspecialchars($alt['kelas']) ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                    $bidang = $alt['bidang_osn'];
                                    $bidang_colors = [
                                        'IPA' => ['bg' => 'bg-success', 'icon' => 'bi-flask'],
                                        'IPS' => ['bg' => 'bg-info', 'icon' => 'bi-globe-americas'],
                                        'Matematika' => ['bg' => 'bg-warning', 'icon' => 'bi-calculator'],
                                    ];
                                    $bc = $bidang_colors[$bidang] ?? ['bg' => 'bg-secondary', 'icon' => 'bi-tag'];
                                ?>
                                <span class="badge <?= $bc['bg'] ?> bg-opacity-10 text-<?= str_replace('bg-', '', $bc['bg']) ?> fw-semibold px-3 py-2">
                                    <i class="bi <?= $bc['icon'] ?> me-1"></i> <?= htmlspecialchars($bidang) ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="?page=alternatif&action=edit&id=<?= $alt['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                        onclick="confirmDelete(<?= $alt['id'] ?>, '<?= htmlspecialchars(addslashes($alt['nama_siswa'])) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                <?php if (!empty($filter_bidang)): ?>
                                    Belum ada siswa di bidang <strong><?= htmlspecialchars($filter_bidang) ?></strong>.
                                <?php else: ?>
                                    Belum ada data siswa.
                                <?php endif; ?>
                                <br>
                                <a href="?page=alternatif&action=create" class="text-primary">Tambah sekarang</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (count($alternatif_list) > 0): ?>
    <div class="card-footer bg-transparent border-top py-3 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">Menampilkan <strong><?= count($alternatif_list) ?></strong> siswa<?= !empty($filter_bidang) ? " (bidang $filter_bidang)" : '' ?></span>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3"><i class="bi bi-exclamation-triangle-fill fs-1"></i></div>
                <h6 class="fw-bold mb-2">Hapus Data Siswa?</h6>
                <p class="text-muted small mb-3">Data siswa <strong id="deleteNama"></strong> akan dihapus secara permanen beserta data penilaian terkait.</p>
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
function confirmDelete(id, nama) {
    document.getElementById('deleteNama').textContent = nama;
    document.getElementById('deleteForm').action = '?page=alternatif&action=delete&id=' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
