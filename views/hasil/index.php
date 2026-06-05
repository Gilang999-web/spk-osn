

<?php
$kriteria_list  = $data['kriteria'] ?? [];
$bidang_list    = $data['bidang_list'] ?? ['IPA', 'IPS', 'Matematika'];
$success        = $data['success'] ?? '';
$error          = $data['error'] ?? '';
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
        <h4 class="mb-1 fw-bold"><i class="bi bi-trophy me-2"></i>Hasil Ranking OSN</h4>
        <p class="text-muted mb-0 small">Rekomendasi 5 siswa terbaik per bidang OSN berdasarkan metode AHP-SAW.</p>
    </div>
    <a href="?page=saw" class="btn btn-outline-primary">
        <i class="bi bi-calculator me-1"></i> SAW Analysis
    </a>
</div>

<!-- Ringkasan Card -->
<div class="row g-4 mb-4">
    <?php 
    $icons = ['IPA' => 'bi-mortarboard', 'IPS' => 'bi-globe-americas', 'Matematika' => 'bi-calculator'];
    $colors = ['IPA' => '#2563eb', 'IPS' => '#059669', 'Matematika' => '#7c3aed'];
    $labels = ['IPA' => 'Ilmu Pengetahuan Alam', 'IPS' => 'Ilmu Pengetahuan Sosial', 'Matematika' => 'Matematika'];
    
    foreach ($bidang_list as $bidang):
        $key = strtolower($bidang);
        $has_data = $data['has_data_' . $key] ?? false;
        $last_calc = $data['last_calc_' . $key] ?? '';
        $ranking_all = $data['ranking_' . $key] ?? [];
        $top5 = $data['top5_' . $key] ?? [];
        $total = count($ranking_all);
        $color = $colors[$bidang];
    ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width:44px;height:44px;background:<?= $color ?>15;">
                        <i class="bi <?= $icons[$bidang] ?>" style="color:<?= $color ?>;font-size:1.3rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold"><?= $labels[$bidang] ?></h6>
                        <span class="text-muted small">
                            <?php if ($has_data): ?>
                                <?= $total ?> siswa diranking
                                <?php if ($last_calc): ?> — Terakhir: <?= htmlspecialchars($last_calc) ?><?php endif; ?>
                            <?php else: ?>
                                Belum dihitung
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div>
                    <?php if ($has_data): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Selesai</span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><i class="bi bi-clock me-1"></i> Belum</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($has_data && !empty($top5)): ?>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="80">Ranking</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Nilai Vi</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top5 as $r): ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($r['ranking'] == 1): ?>
                                        <span class="badge bg-warning text-dark px-3 py-2">🥇 1</span>
                                    <?php elseif ($r['ranking'] == 2): ?>
                                        <span class="badge bg-secondary px-3 py-2">🥈 2</span>
                                    <?php elseif ($r['ranking'] == 3): ?>
                                        <span class="badge bg-danger bg-opacity-75 px-3 py-2">🥉 3</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary bg-opacity-25 text-primary px-3 py-2"><?= $r['ranking'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold"><?= htmlspecialchars($r['nama_siswa']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($r['kelas']) ?></td>
                                <td class="text-center fw-bold" style="color:<?= $color ?>">
                                    <?= number_format($r['nilai_preferensi'], 6) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Terpilih
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if ($total > 5): ?>
                            <tr class="text-muted">
                                <td colspan="5" class="text-center small py-2">
                                    <i class="bi bi-three-dots"></i> dan <?= $total - 5 ?> siswa lainnya
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-end py-2">
                <a href="?page=saw&bidang_osn=<?= urlencode($bidang) ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-1"></i> Lihat Detail Perhitungan
                </a>
            </div>

            <?php else: ?>
            <div class="card-body text-center py-4">
                <i class="bi bi-inbox fs-3 text-muted d-block mb-2"></i>
                <p class="text-muted small mb-2">Belum ada hasil ranking untuk bidang ini.</p>
                <a href="?page=saw&bidang_osn=<?= urlencode($bidang) ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-calculator me-1"></i> Hitung SAW
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-between">
    <a href="?page=saw" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke SAW Analysis
    </a>
    <a href="?page=laporan" class="btn btn-primary">
        <i class="bi bi-printer me-1"></i> Cetak Laporan
    </a>
</div>


