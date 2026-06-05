<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pendukung Keputusan - SPK OSN</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header text-center">
            <img src="assets/images/logo_nesatma.jpg" alt="Logo SMP 1 Manonjaya" style="width: 70px; height: auto; margin-bottom: 12px; border-radius: 8px;">
            <a href="?page=dashboard" class="sidebar-brand d-block" style="font-size: 1.1rem; line-height: 1.3; white-space: normal;">Sistem Pendukung Keputusan</a>
            <div class="small text-muted mt-2">SMP 1 Manonjaya</div>
        </div>
        
        <?php $current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; ?>
        
        <ul class="list-unstyled components">
            <li class="<?= ($current_page == 'dashboard') ? 'active' : '' ?>">
                <a href="?page=dashboard"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            </li>
            <li class="nav-section-title">DATA MASTER</li>
            <li class="<?= ($current_page == 'kriteria') ? 'active' : '' ?>">
                <a href="?page=kriteria"><i class="bi bi-list-check"></i> Data Kriteria</a>
            </li>
            <li class="<?= ($current_page == 'alternatif') ? 'active' : '' ?>">
                <a href="?page=alternatif"><i class="bi bi-people"></i> Data Alternatif</a>
            </li>
            <li class="<?= ($current_page == 'penilaian') ? 'active' : '' ?>">
                <a href="?page=penilaian"><i class="bi bi-pencil-square"></i> Input Penilaian</a>
            </li>
            <li class="nav-section-title">PERHITUNGAN</li>
            <li class="<?= ($current_page == 'ahp' || $current_page == 'ahp_hasil') ? 'active' : '' ?>">
                <a href="?page=ahp"><i class="bi bi-diagram-3"></i> AHP Analysis</a>
            </li>
            <li class="<?= ($current_page == 'saw') ? 'active' : '' ?>">
                <a href="?page=saw"><i class="bi bi-calculator"></i> SAW Analysis</a>
            </li>
            <li class="nav-section-title">LAPORAN</li>
            <li class="<?= ($current_page == 'hasil') ? 'active' : '' ?>">
                <a href="?page=hasil"><i class="bi bi-trophy"></i> Hasil Ranking</a>
            </li>
            <li class="<?= ($current_page == 'laporan') ? 'active' : '' ?>">
                <a href="?page=laporan"><i class="bi bi-printer"></i> Cetak Laporan</a>
            </li>
        </ul>
        
        <div class="mt-auto sidebar-footer">
            <a href="?page=profil" class="text-decoration-none">
                <div class="sidebar-user-info p-2 rounded hover-bg-light transition">
                    <div class="user-avatar" style="overflow:hidden; display:flex; align-items:center; justify-content:center;">
                        <?php if(!empty($_SESSION['foto'])): ?>
                            <img src="assets/images/uploads/<?= htmlspecialchars($_SESSION['foto']) ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="fw-semibold small text-dark"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></div>
                        <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($_SESSION['role'] ?? 'Administrator') ?></div>
                    </div>
                </div>
            </a>
            <a href="?page=logout" class="btn btn-outline-danger btn-sm w-100 mt-2"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-muted p-0 me-3 d-md-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="topbar-icon"><i class="bi bi-bell"></i></a>
                <a href="#" class="topbar-icon"><i class="bi bi-question-circle"></i></a>
                <div class="topbar-divider"></div>
                <a href="?page=profil" class="text-decoration-none text-dark d-flex align-items-center rounded p-1 hover-bg-light transition">
                    <div class="text-end me-2 d-none d-md-block">
                        <div class="fw-bold lh-1 small"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></div>
                        <small class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($_SESSION['role'] ?? 'Administrator') ?></small>
                    </div>
                    <div class="user-avatar-sm" style="overflow:hidden; display:flex; align-items:center; justify-content:center;">
                        <?php if(!empty($_SESSION['foto'])): ?>
                            <img src="assets/images/uploads/<?= htmlspecialchars($_SESSION['foto']) ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
