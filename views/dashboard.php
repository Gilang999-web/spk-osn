<?php require_once 'views/layouts/header.php'; ?>

<?php
// Data dari controller sudah tersedia via $data
$total_kriteria   = $data['total_kriteria'] ?? 0;
$total_alternatif = $data['total_alternatif'] ?? 0;
$ahp_done         = $data['ahp_done'] ?? false;
$total_penilaian  = $data['total_penilaian'] ?? 0;
$total_hasil      = $data['total_hasil'] ?? 0;
$count_ipa        = $data['count_ipa'] ?? 0;
$count_ips        = $data['count_ips'] ?? 0;
$count_mtk        = $data['count_mtk'] ?? 0;
$kriteria_list    = $data['kriteria'] ?? [];
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Dashboard</h4>
        <p class="text-muted mb-0 small">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?> 👋</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-muted border px-3 py-2"><i class="bi bi-calendar3 me-1"></i> <?= date('d M Y') ?></span>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Total Kriteria</p>
                        <h3 class="fw-bold mb-1"><?= $total_kriteria ?></h3>
                        <span class="badge bg-primary bg-opacity-10 text-primary small">C1 – C5 Fix</span>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-list-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Total Alternatif</p>
                        <h3 class="fw-bold mb-1"><?= $total_alternatif ?></h3>
                        <span class="badge bg-info bg-opacity-10 text-info small"><?= $total_alternatif > 0 ? 'Siswa terdaftar' : 'Belum ada data' ?></span>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Status AHP</p>
                        <h3 class="fw-bold mb-1"><?= $ahp_done ? 'Selesai' : 'Belum' ?></h3>
                        <?php if ($ahp_done): ?>
                            <span class="badge bg-success bg-opacity-10 text-success small"><i class="bi bi-check-circle me-1"></i>Konsisten</span>
                        <?php else: ?>
                            <span class="badge bg-warning bg-opacity-10 text-warning small"><i class="bi bi-exclamation-circle me-1"></i>Perlu input</span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-icon <?= $ahp_done ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' ?>">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Hasil Ranking</p>
                        <h3 class="fw-bold mb-1"><?= $total_hasil ?></h3>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary small"><?= $total_hasil > 0 ? 'Data tersedia' : 'Belum dihitung' ?></span>
                    </div>
                    <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-trophy"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-4 mb-4">
    <!-- Quick Actions -->
    <div class="col-lg-8">
        <div class="card border-0 h-100">
            <div class="card-body p-4">
                <h5 class="card-title mb-1">Alur Kerja SPK</h5>
                <p class="text-muted small mb-4">Ikuti langkah-langkah berikut secara berurutan untuk mendapatkan hasil perankingan.</p>

                <div class="workflow-steps">
                    <!-- Step 1 -->
                    <div class="workflow-step <?= $total_kriteria >= 5 ? 'completed' : 'active' ?>">
                        <div class="step-number"><?= $total_kriteria >= 5 ? '<i class="bi bi-check-lg"></i>' : '1' ?></div>
                        <div class="step-content">
                            <h6 class="mb-1">Data Kriteria</h6>
                            <p class="text-muted small mb-0">5 kriteria penilaian (C1 – C5) sudah diatur.</p>
                        </div>
                        <a href="?page=kriteria" class="btn btn-sm btn-outline-primary ms-auto">Lihat</a>
                    </div>
                    <!-- Step 2 -->
                    <div class="workflow-step <?= $total_alternatif > 0 ? 'completed' : ($total_kriteria >= 5 ? 'active' : '') ?>">
                        <div class="step-number"><?= $total_alternatif > 0 ? '<i class="bi bi-check-lg"></i>' : '2' ?></div>
                        <div class="step-content">
                            <h6 class="mb-1">Data Alternatif (Siswa)</h6>
                            <p class="text-muted small mb-0"><?= $total_alternatif > 0 ? "$total_alternatif siswa terdaftar." : 'Input data siswa calon peserta OSN.' ?></p>
                        </div>
                        <a href="?page=alternatif" class="btn btn-sm btn-outline-primary ms-auto">Kelola</a>
                    </div>
                    <!-- Step 3 -->
                    <div class="workflow-step <?= $total_penilaian > 0 ? 'completed' : ($total_alternatif > 0 ? 'active' : '') ?>">
                        <div class="step-number"><?= $total_penilaian > 0 ? '<i class="bi bi-check-lg"></i>' : '3' ?></div>
                        <div class="step-content">
                            <h6 class="mb-1">Input Penilaian</h6>
                            <p class="text-muted small mb-0"><?= $total_penilaian > 0 ? "$total_penilaian data penilaian tersimpan." : 'Masukkan nilai C1–C5 setiap siswa.' ?></p>
                        </div>
                        <a href="?page=penilaian" class="btn btn-sm btn-outline-primary ms-auto">Input</a>
                    </div>
                    <!-- Step 4 -->
                    <div class="workflow-step <?= $ahp_done ? 'completed' : ($total_penilaian > 0 ? 'active' : '') ?>">
                        <div class="step-number"><?= $ahp_done ? '<i class="bi bi-check-lg"></i>' : '4' ?></div>
                        <div class="step-content">
                            <h6 class="mb-1">Perhitungan AHP</h6>
                            <p class="text-muted small mb-0"><?= $ahp_done ? 'Bobot kriteria sudah ditentukan.' : 'Isi matriks perbandingan berpasangan.' ?></p>
                        </div>
                        <a href="?page=ahp" class="btn btn-sm btn-outline-primary ms-auto">Hitung</a>
                    </div>
                    <!-- Step 5 -->
                    <div class="workflow-step <?= $total_hasil > 0 ? 'completed' : ($ahp_done ? 'active' : '') ?>">
                        <div class="step-number"><?= $total_hasil > 0 ? '<i class="bi bi-check-lg"></i>' : '5' ?></div>
                        <div class="step-content">
                            <h6 class="mb-1">Perhitungan SAW & Ranking</h6>
                            <p class="text-muted small mb-0"><?= $total_hasil > 0 ? 'Ranking telah dihasilkan!' : 'Hitung ranking per bidang OSN.' ?></p>
                        </div>
                        <a href="?page=saw" class="btn btn-sm btn-outline-primary ms-auto">Proses</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bidang OSN Info Card -->
    <div class="col-lg-4">
        <div class="card border-0 card-gradient-blue h-100">
            <div class="card-body p-4 text-white">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-mortarboard fs-4 me-2"></i>
                    <h5 class="mb-0 fw-bold">Distribusi Bidang OSN</h5>
                </div>
                <p class="opacity-75 small mb-4">Jumlah siswa yang terdaftar per bidang lomba OSN.</p>

                <?php
                $max_siswa = max($count_ipa, $count_ips, $count_mtk, 1);
                $bidang_data = [
                    ['nama' => 'IPA',         'count' => $count_ipa, 'icon' => 'bi-flask'],
                    ['nama' => 'IPS',         'count' => $count_ips, 'icon' => 'bi-globe-americas'],
                    ['nama' => 'Matematika',  'count' => $count_mtk, 'icon' => 'bi-calculator'],
                ];
                ?>
                <?php foreach ($bidang_data as $b): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small"><i class="bi <?= $b['icon'] ?> me-1"></i> <?= $b['nama'] ?></span>
                        <span class="fw-bold"><?= $b['count'] ?> Siswa</span>
                    </div>
                    <div class="progress bg-white bg-opacity-25" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-white" style="width: <?= $max_siswa > 0 ? round($b['count'] / $max_siswa * 100) : 0 ?>%; border-radius: 4px;"></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="text-center mt-4">
                    <a href="?page=alternatif" class="btn btn-light btn-sm fw-semibold px-4"><i class="bi bi-plus-circle me-1"></i> Tambah Siswa</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kriteria Table -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="card-title mb-1">Daftar Kriteria Penilaian</h5>
                        <p class="text-muted small mb-0">5 kriteria yang digunakan dalam perhitungan AHP-SAW.</p>
                    </div>
                    <a href="?page=kriteria" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-right me-1"></i> Kelola</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th class="border-0 ps-0">Kode</th>
                                <th class="border-0">Nama Kriteria</th>
                                <th class="border-0">Jenis</th>
                                <th class="border-0 text-end pe-0">Bobot (AHP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($kriteria_list) > 0): ?>
                                <?php foreach ($kriteria_list as $k): ?>
                                <tr>
                                    <td class="ps-0"><span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-2"><?= htmlspecialchars($k['kode']) ?></span></td>
                                    <td class="fw-medium"><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success">Benefit</span></td>
                                    <td class="text-end pe-0">
                                        <?php if ($k['bobot']): ?>
                                            <span class="fw-bold"><?= number_format($k['bobot'], 4) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">– belum dihitung –</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data kriteria. <a href="?page=kriteria">Tambah sekarang</a>.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
