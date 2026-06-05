

<?php
$kriteria_list      = $data['kriteria'] ?? [];
$bidang_osn         = $data['bidang_osn'] ?? '';
$bobot_terisi       = $data['bobot_terisi'] ?? false;
$success            = $data['success'] ?? '';
$error              = $data['error'] ?? '';
$siswa              = $data['siswa'] ?? [];
$matriks_keputusan  = $data['matriks_keputusan'] ?? [];
$matriks_normal     = $data['matriks_normal'] ?? [];
$max_kolom          = $data['max_kolom'] ?? [];
$min_kolom          = $data['min_kolom'] ?? [];
$bobot              = $data['bobot'] ?? [];
$vi                 = $data['vi'] ?? [];
$detail_vi          = $data['detail_vi'] ?? [];
$ranking            = $data['ranking'] ?? [];
$penilaian_lengkap  = $data['penilaian_lengkap'] ?? false;
$sudah_disimpan     = $data['sudah_disimpan'] ?? false;
$last_calculated    = $data['last_calculated'] ?? '';
$n                  = count($siswa);
$m                  = count($kriteria_list);
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
        <h4 class="mb-1 fw-bold">SAW — Perhitungan Ranking</h4>
        <p class="text-muted mb-0 small">Simple Additive Weighting: normalisasi matriks keputusan, hitung Vi, dan perangkingan per bidang OSN.</p>
    </div>
    <a href="?page=ahp_hasil" class="btn btn-outline-secondary">
        <i class="bi bi-diagram-3 me-1"></i> Lihat Bobot AHP
    </a>
</div>

<!-- Bobot AHP belum terisi -->
<?php if (!$bobot_terisi): ?>
<div class="card border-0 mb-4">
    <div class="card-body text-center py-5">
        <i class="bi bi-exclamation-triangle fs-1 text-warning d-block mb-3"></i>
        <h6 class="fw-bold">Bobot AHP Belum Tersedia</h6>
        <p class="text-muted small mb-3">Anda perlu menjalankan perhitungan AHP terlebih dahulu agar bobot kriteria tersedia untuk SAW.</p>
        <a href="?page=ahp" class="btn btn-primary">
            <i class="bi bi-diagram-3 me-1"></i> Ke Halaman AHP
        </a>
    </div>
</div>
<?php else: ?>

<!-- Filter Bidang OSN -->
<div class="card border-0 mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <form action="" method="GET" id="filterForm">
                    <input type="hidden" name="page" value="saw">
                    <label for="bidang_osn" class="form-label fw-semibold">Pilih Bidang OSN</label>
                    <select name="bidang_osn" id="bidang_osn" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Bidang OSN --</option>
                        <option value="IPA" <?= ($bidang_osn == 'IPA') ? 'selected' : '' ?>>Ilmu Pengetahuan Alam (IPA)</option>
                        <option value="IPS" <?= ($bidang_osn == 'IPS') ? 'selected' : '' ?>>Ilmu Pengetahuan Sosial (IPS)</option>
                        <option value="Matematika" <?= ($bidang_osn == 'Matematika') ? 'selected' : '' ?>>Matematika</option>
                    </select>
                </form>
            </div>
            <div class="col-md-3">
                <button type="submit" form="filterForm" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Tampilkan
                </button>
            </div>
            <?php if (!empty($bidang_osn) && $penilaian_lengkap && $n > 0): ?>
            <div class="col-md-3">
                <form action="?page=saw&action=hitung" method="POST">
                    <input type="hidden" name="bidang_osn" value="<?= htmlspecialchars($bidang_osn) ?>">
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Hitung SAW dan simpan ranking untuk bidang <?= htmlspecialchars($bidang_osn) ?>?')">
                        <i class="bi bi-calculator me-1"></i> Hitung & Simpan
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bobot AHP Info Card -->
<?php if (!empty($bidang_osn)): ?>
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-info-circle me-2"></i> Bobot Kriteria (Hasil AHP)
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <?php foreach ($kriteria_list as $idx => $k): ?>
            <div class="col">
                <div class="border rounded p-2 text-center">
                    <div class="fw-bold text-primary"><?= htmlspecialchars($k['kode']) ?></div>
                    <div class="small text-muted"><?= htmlspecialchars($k['nama_kriteria']) ?></div>
                    <div class="fw-bold mt-1"><?= number_format($k['bobot'], 4) ?></div>
                    <span class="badge bg-<?= $k['jenis'] === 'Benefit' ? 'success' : 'danger' ?> bg-opacity-75" style="font-size:0.65rem;"><?= $k['jenis'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Konten Perhitungan SAW -->
<?php if (!empty($bidang_osn) && $n > 0 && $penilaian_lengkap): ?>

<!-- ============================== -->
<!-- TABEL 1: Matriks Keputusan (X) -->
<!-- ============================== -->
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-grid-3x3 me-2"></i> 1. Matriks Keputusan (X) — Bidang <?= htmlspecialchars($bidang_osn) ?>
        </h6>
        <p class="text-muted small mb-0 mt-1">Nilai asli setiap siswa pada setiap kriteria</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th>Nama Siswa</th>
                        <?php foreach ($kriteria_list as $k): ?>
                            <th class="text-center"><?= htmlspecialchars($k['kode']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($siswa[$i]['nama_siswa']) ?></div>
                            <div class="small text-muted">Kelas <?= htmlspecialchars($siswa[$i]['kelas']) ?></div>
                        </td>
                        <?php for ($j = 0; $j < $m; $j++): ?>
                            <td class="text-center"><?= number_format($matriks_keputusan[$i][$j], 4) ?></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                    <!-- Baris Max/Min -->
                    <tr class="table-warning fw-bold">
                        <td colspan="2" class="text-center">Max</td>
                        <?php for ($j = 0; $j < $m; $j++): ?>
                            <td class="text-center"><?= number_format($max_kolom[$j], 4) ?></td>
                        <?php endfor; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================== -->
<!-- TABEL 2: Matriks Normalisasi (R) -->
<!-- ============================== -->
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-table me-2"></i> 2. Matriks Normalisasi (R)
        </h6>
        <p class="text-muted small mb-0 mt-1">Benefit: r<sub>ij</sub> = X<sub>ij</sub> / Max(X<sub>j</sub>) &nbsp;|&nbsp; Cost: r<sub>ij</sub> = Min(X<sub>j</sub>) / X<sub>ij</sub></p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th>Nama Siswa</th>
                        <?php foreach ($kriteria_list as $k): ?>
                            <th class="text-center"><?= htmlspecialchars($k['kode']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($siswa[$i]['nama_siswa']) ?></div>
                        </td>
                        <?php for ($j = 0; $j < $m; $j++): ?>
                            <td class="text-center <?= ($matriks_normal[$i][$j] == 1) ? 'bg-success bg-opacity-10 fw-bold text-success' : '' ?>">
                                <?= number_format($matriks_normal[$i][$j], 4) ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================== -->
<!-- TABEL 3: Perhitungan Nilai Preferensi (Vi) -->
<!-- ============================== -->
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-calculator me-2"></i> 3. Perhitungan Nilai Preferensi (Vi)
        </h6>
        <p class="text-muted small mb-0 mt-1">Vi = Σ (W<sub>j</sub> × r<sub>ij</sub>) untuk setiap siswa</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th>Nama Siswa</th>
                        <?php foreach ($kriteria_list as $k): ?>
                            <th class="text-center">
                                W<sub><?= htmlspecialchars($k['kode']) ?></sub> × R
                            </th>
                        <?php endforeach; ?>
                        <th class="text-center bg-primary bg-opacity-10 text-primary">Vi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($siswa[$i]['nama_siswa']) ?></td>
                        <?php for ($j = 0; $j < $m; $j++): ?>
                            <td class="text-center"><?= number_format($detail_vi[$i][$j], 4) ?></td>
                        <?php endfor; ?>
                        <td class="text-center fw-bold text-primary"><?= number_format($vi[$i], 4) ?></td>
                    </tr>
                    <?php endfor; ?>
                    <!-- Baris Bobot -->
                    <tr class="table-info">
                        <td colspan="2" class="text-center fw-bold">Bobot (W)</td>
                        <?php for ($j = 0; $j < $m; $j++): ?>
                            <td class="text-center fw-bold"><?= number_format($bobot[$j], 4) ?></td>
                        <?php endfor; ?>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================== -->
<!-- TABEL 4: Ranking -->
<!-- ============================== -->
<div class="card border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-trophy me-2"></i> 4. Hasil Perangkingan — Bidang <?= htmlspecialchars($bidang_osn) ?>
        </h6>
        <p class="text-muted small mb-0 mt-1">Diurutkan berdasarkan nilai Vi terbesar</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
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
                    <?php foreach ($ranking as $r): ?>
                    <?php $isTop5 = $r['ranking'] <= 5; ?>
                    <tr class="<?= $isTop5 ? 'table-success' : '' ?>">
                        <td class="text-center">
                            <?php if ($r['ranking'] == 1): ?>
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6">🥇 1</span>
                            <?php elseif ($r['ranking'] == 2): ?>
                                <span class="badge bg-secondary px-3 py-2 fs-6">🥈 2</span>
                            <?php elseif ($r['ranking'] == 3): ?>
                                <span class="badge bg-danger bg-opacity-75 px-3 py-2 fs-6">🥉 3</span>
                            <?php else: ?>
                                <span class="fw-bold"><?= $r['ranking'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold"><?= htmlspecialchars($r['nama_siswa']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($r['kelas']) ?></td>
                        <td class="text-center fw-bold text-primary"><?= number_format($r['vi'], 6) ?></td>
                        <td class="text-center">
                            <?php if ($isTop5): ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> Terpilih
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-50">Tidak Terpilih</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Status Penyimpanan -->
<?php if ($sudah_disimpan): ?>
<div class="alert border-0 bg-success bg-opacity-10 text-success d-flex align-items-center py-3" role="alert">
    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
    <div>
        <strong>✅ Hasil sudah disimpan ke database</strong>
        <div class="small opacity-75">Terakhir dihitung: <?= htmlspecialchars($last_calculated) ?></div>
    </div>
</div>
<?php else: ?>
<div class="alert border-0 bg-warning bg-opacity-10 text-warning d-flex align-items-center py-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
    <div>
        <strong>⚠️ Hasil belum disimpan</strong>
        <div class="small opacity-75">Tekan tombol "Hitung & Simpan" di atas untuk menyimpan ranking ke database.</div>
    </div>
</div>
<?php endif; ?>

<!-- Action Buttons -->
<div class="d-flex justify-content-between">
    <a href="?page=penilaian&bidang_osn=<?= urlencode($bidang_osn) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-pencil-square me-1"></i> Edit Penilaian
    </a>
    <a href="?page=hasil" class="btn btn-primary">
        Lihat Hasil Ranking Semua Bidang <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

<?php elseif (!empty($bidang_osn) && $n == 0): ?>
<!-- Tidak ada siswa -->
<div class="card border-0">
    <div class="card-body text-center py-5">
        <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
        <h6 class="fw-bold">Belum Ada Data Siswa</h6>
        <p class="text-muted small mb-3">Belum ada siswa yang terdaftar di bidang <?= htmlspecialchars($bidang_osn) ?>.</p>
        <a href="?page=alternatif&action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
        </a>
    </div>
</div>

<?php elseif (!empty($bidang_osn) && !$penilaian_lengkap): ?>
<!-- Penilaian belum lengkap -->
<div class="card border-0">
    <div class="card-body text-center py-5">
        <i class="bi bi-clipboard-x fs-1 text-warning d-block mb-3"></i>
        <h6 class="fw-bold">Penilaian Belum Lengkap</h6>
        <p class="text-muted small mb-3">Pastikan semua siswa bidang <?= htmlspecialchars($bidang_osn) ?> sudah dinilai pada semua kriteria (C1-C5).</p>
        <a href="?page=penilaian&bidang_osn=<?= urlencode($bidang_osn) ?>" class="btn btn-primary">
            <i class="bi bi-pencil-square me-1"></i> Input Penilaian
        </a>

        <!-- Tetap tampilkan matriks keputusan yang ada (partial) -->
        <?php if (!empty($matriks_keputusan)): ?>
        <div class="mt-4 text-start">
            <div class="card border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 text-primary">
                        <i class="bi bi-grid-3x3 me-2"></i> Matriks Keputusan (Belum Lengkap)
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Nama Siswa</th>
                                    <?php foreach ($kriteria_list as $k): ?>
                                        <th class="text-center"><?= htmlspecialchars($k['kode']) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < $n; $i++): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($siswa[$i]['nama_siswa']) ?></td>
                                    <?php for ($j = 0; $j < $m; $j++): ?>
                                        <td class="text-center <?= ($matriks_keputusan[$i][$j] == 0) ? 'bg-danger bg-opacity-10 text-danger' : '' ?>">
                                            <?= ($matriks_keputusan[$i][$j] == 0) ? '<i class="bi bi-x-circle"></i>' : number_format($matriks_keputusan[$i][$j], 4) ?>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php endif; /* end bobot_terisi check */ ?>


