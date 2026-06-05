<?php require_once 'views/layouts/header.php'; ?>

<?php
$kriteria_list            = $data['kriteria'] ?? [];
$has_data                 = $data['has_data'] ?? false;
$success                  = $data['success'] ?? '';
$error                    = $data['error'] ?? '';
$n                        = count($kriteria_list);

// Data perhitungan
$matriks_perbandingan     = $data['matriks_perbandingan'] ?? [];
$jumlah_kolom             = $data['jumlah_kolom'] ?? [];
$matriks_normal           = $data['matriks_normal'] ?? [];
$jumlah_baris_normal      = $data['jumlah_baris_normal'] ?? [];
$prioritas                = $data['prioritas'] ?? [];
$matriks_penjumlahan      = $data['matriks_penjumlahan'] ?? [];
$jumlah_penjumlahan_baris = $data['jumlah_penjumlahan_baris'] ?? [];
$rasio                    = $data['rasio'] ?? [];
$lambda_max               = $data['lambda_max'] ?? 0;
$ci                       = $data['ci'] ?? 0;
$ri                       = $data['ri'] ?? 1.12;
$cr                       = $data['cr'] ?? 0;
$konsisten                = $data['konsisten'] ?? false;
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
        <h4 class="mb-1 fw-bold">AHP — Hasil Perhitungan</h4>
        <p class="text-muted mb-0 small">Normalisasi matriks, prioritas kriteria, dan uji konsistensi (CI/CR).</p>
    </div>
    <a href="?page=ahp" class="btn btn-outline-primary">
        <i class="bi bi-pencil-square me-1"></i> Edit Matriks
    </a>
</div>

<?php if (!$has_data): ?>
    <div class="card border-0">
        <div class="card-body text-center py-5">
            <i class="bi bi-diagram-3 fs-1 text-muted d-block mb-3"></i>
            <h6 class="fw-bold">Belum Ada Data Matriks</h6>
            <p class="text-muted small mb-3">Anda perlu mengisi matriks perbandingan berpasangan terlebih dahulu.</p>
            <a href="?page=ahp" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Input Matriks Sekarang
            </a>
        </div>
    </div>
<?php else: ?>

<!-- ============================== -->
<!-- TABEL 1: Matriks Perbandingan Berpasangan + Jumlah Kolom -->
<!-- ============================== -->
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-grid-3x3 me-2"></i> 1. Matriks Perbandingan Berpasangan
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Kriteria</th>
                        <?php foreach ($kriteria_list as $k): ?>
                            <th class="text-center"><?= htmlspecialchars($k['kode']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <td class="fw-semibold text-center table-light"><?= htmlspecialchars($kriteria_list[$i]['kode']) ?></td>
                        <?php for ($j = 0; $j < $n; $j++): ?>
                            <td class="text-center <?= ($i == $j) ? 'bg-light' : '' ?>">
                                <?= number_format($matriks_perbandingan[$i][$j], 4) ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                    <!-- Jumlah Kolom -->
                    <tr class="table-warning fw-bold">
                        <td class="text-center">Jumlah</td>
                        <?php for ($j = 0; $j < $n; $j++): ?>
                            <td class="text-center"><?= number_format($jumlah_kolom[$j], 4) ?></td>
                        <?php endfor; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================== -->
<!-- TABEL 2: Matriks Normalisasi + Jumlah + Prioritas -->
<!-- ============================== -->
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-table me-2"></i> 2. Matriks Nilai Kriteria (Normalisasi)
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Kriteria</th>
                        <?php foreach ($kriteria_list as $k): ?>
                            <th class="text-center"><?= htmlspecialchars($k['kode']) ?></th>
                        <?php endforeach; ?>
                        <th class="text-center bg-primary bg-opacity-10 text-primary">Jumlah</th>
                        <th class="text-center bg-primary bg-opacity-10 text-primary">Prioritas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <td class="fw-semibold text-center table-light"><?= htmlspecialchars($kriteria_list[$i]['kode']) ?></td>
                        <?php for ($j = 0; $j < $n; $j++): ?>
                            <td class="text-center"><?= number_format($matriks_normal[$i][$j], 4) ?></td>
                        <?php endfor; ?>
                        <td class="text-center fw-semibold"><?= number_format($jumlah_baris_normal[$i], 4) ?></td>
                        <td class="text-center fw-bold text-primary"><?= number_format($prioritas[$i], 4) ?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================== -->
<!-- TABEL 3: Matriks Penjumlahan Setiap Baris -->
<!-- Setiap sel = matriks_perbandingan[i][j] × prioritas[j] -->
<!-- ============================== -->
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-calculator me-2"></i> 3. Matriks Penjumlahan Setiap Baris
        </h6>
        <p class="text-muted small mb-0 mt-1">Setiap sel = nilai matriks perbandingan × prioritas kriteria kolom</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Kriteria</th>
                        <?php foreach ($kriteria_list as $k): ?>
                            <th class="text-center"><?= htmlspecialchars($k['kode']) ?></th>
                        <?php endforeach; ?>
                        <th class="text-center bg-primary bg-opacity-10 text-primary">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <td class="fw-semibold text-center table-light"><?= htmlspecialchars($kriteria_list[$i]['kode']) ?></td>
                        <?php for ($j = 0; $j < $n; $j++): ?>
                            <td class="text-center"><?= number_format($matriks_penjumlahan[$i][$j], 4) ?></td>
                        <?php endfor; ?>
                        <td class="text-center fw-bold text-primary"><?= number_format($jumlah_penjumlahan_baris[$i], 4) ?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================== -->
<!-- TABEL 4: Perhitungan Rasio Konsistensi -->
<!-- ============================== -->
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-shield-check me-2"></i> 4. Perhitungan Rasio Konsistensi
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Kriteria</th>
                        <th class="text-center">Penjumlahan Setiap Baris</th>
                        <th class="text-center"></th>
                        <th class="text-center">Prioritas</th>
                        <th class="text-center"></th>
                        <th class="text-center">Hasil (Penjumlahan + Prioritas)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <td class="fw-semibold text-center table-light"><?= htmlspecialchars($kriteria_list[$i]['kode']) ?></td>
                        <td class="text-center"><?= number_format($jumlah_penjumlahan_baris[$i], 4) ?></td>
                        <td class="text-center text-muted fw-bold">+</td>
                        <td class="text-center"><?= number_format($prioritas[$i], 4) ?></td>
                        <td class="text-center text-muted fw-bold">=</td>
                        <td class="text-center fw-bold"><?= number_format($rasio[$i], 4) ?></td>
                    </tr>
                    <?php endfor; ?>
                    <tr class="table-warning fw-bold">
                        <td class="text-center" colspan="5">Total</td>
                        <td class="text-center"><?= number_format(array_sum($rasio), 4) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================== -->
<!-- HASIL: λmax, CI, CR, Status -->
<!-- ============================== -->
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-check2-all me-2"></i> 5. Hasil Uji Konsistensi
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted small mb-1">λ max</div>
                    <div class="fs-5 fw-bold text-dark"><?= number_format($lambda_max, 4) ?></div>
                    <div class="text-muted" style="font-size:0.7rem;">Total Hasil / n = <?= number_format(array_sum($rasio), 4) ?> / <?= $n ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted small mb-1">CI (Consistency Index)</div>
                    <div class="fs-5 fw-bold text-dark"><?= number_format($ci, 4) ?></div>
                    <div class="text-muted" style="font-size:0.7rem;">(λmax − n) / (n − 1)</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted small mb-1">RI (n=<?= $n ?>)</div>
                    <div class="fs-5 fw-bold text-dark"><?= number_format($ri, 2) ?></div>
                    <div class="text-muted" style="font-size:0.7rem;">Random Index</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center <?= $konsisten ? 'border-success' : 'border-danger' ?>">
                    <div class="text-muted small mb-1">CR (Consistency Ratio)</div>
                    <div class="fs-5 fw-bold <?= $konsisten ? 'text-success' : 'text-danger' ?>"><?= number_format($cr, 4) ?></div>
                    <div class="text-muted" style="font-size:0.7rem;">CI / RI</div>
                </div>
            </div>
        </div>

        <!-- Detail Rumus -->
        <div class="p-3 bg-light rounded mb-4" style="font-size:0.85rem;">
            <strong>Detail Perhitungan:</strong><br>
            λmax = <?= number_format(array_sum($rasio), 4) ?> / <?= $n ?> = <strong><?= number_format($lambda_max, 4) ?></strong><br>
            CI = (<?= number_format($lambda_max, 4) ?> − <?= $n ?>) / (<?= $n ?> − 1) = <strong><?= number_format($ci, 4) ?></strong><br>
            CR = <?= number_format($ci, 4) ?> / <?= number_format($ri, 2) ?> = <strong><?= number_format($cr, 4) ?></strong>
        </div>

        <!-- Status Konsistensi -->
        <div class="alert border-0 d-flex align-items-center py-3 <?= $konsisten ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' ?>" role="alert">
            <i class="bi <?= $konsisten ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> fs-4 me-3"></i>
            <div>
                <strong><?= $konsisten ? '✅ KONSISTEN' : '❌ TIDAK KONSISTEN' ?></strong>
                <div class="small opacity-75">
                    <?php if ($konsisten): ?>
                        Nilai CR = <?= number_format($cr, 4) ?> ≤ 0.10. Matriks perbandingan konsisten dan <strong>prioritas telah disimpan</strong> ke database.
                    <?php else: ?>
                        Nilai CR = <?= number_format($cr, 4) ?> > 0.10. Matriks perbandingan TIDAK konsisten. Silakan perbaiki nilai perbandingan.
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prioritas Visual -->
<?php if ($konsisten): ?>
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-bar-chart me-2"></i> Prioritas Kriteria (Hasil Akhir)
        </h6>
    </div>
    <div class="card-body">
        <?php
            $max_prioritas = max($prioritas);
            for ($i = 0; $i < $n; $i++):
                $persen = ($max_prioritas > 0) ? ($prioritas[$i] / $max_prioritas) * 100 : 0;
        ?>
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div>
                    <span class="fw-semibold"><?= htmlspecialchars($kriteria_list[$i]['kode']) ?></span>
                    <span class="text-muted small ms-1">— <?= htmlspecialchars($kriteria_list[$i]['nama_kriteria']) ?></span>
                </div>
                <span class="fw-bold text-primary"><?= number_format($prioritas[$i], 4) ?></span>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar" role="progressbar"
                     style="width: <?= round($persen) ?>%; background: linear-gradient(90deg, #2563eb, #7c3aed);"
                     aria-valuenow="<?= round($persen) ?>" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>

<!-- Action Buttons -->
<div class="d-flex justify-content-between">
    <a href="?page=ahp" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Input Matriks
    </a>
    <?php if ($konsisten): ?>
    <a href="?page=saw" class="btn btn-primary">
        Lanjut ke Perhitungan SAW <i class="bi bi-arrow-right ms-1"></i>
    </a>
    <?php else: ?>
    <a href="?page=ahp" class="btn btn-warning">
        <i class="bi bi-pencil-square me-1"></i> Perbaiki Matriks
    </a>
    <?php endif; ?>
</div>

<?php endif; ?>

<?php require_once 'views/layouts/footer.php'; ?>
