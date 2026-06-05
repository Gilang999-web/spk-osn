<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecisionPro - SPK OSN</title>
    
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
        <div class="sidebar-header">
            <a href="?page=dashboard" class="sidebar-brand">DecisionPro</a>
            <div class="small text-muted mt-1">DSS Engine</div>
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
            <div class="sidebar-user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                    <div class="fw-semibold small"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;">Administrator</div>
                </div>
            </div>
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
                <div class="input-group topbar-search">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Cari data kriteria, siswa...">
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="topbar-icon"><i class="bi bi-bell"></i></a>
                <a href="#" class="topbar-icon"><i class="bi bi-question-circle"></i></a>
                <div class="topbar-divider"></div>
                <div class="d-flex align-items-center">
                    <div class="text-end me-2 d-none d-md-block">
                        <div class="fw-bold lh-1 small"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></div>
                        <small class="text-muted" style="font-size: 0.7rem;">Super User</small>
                    </div>
                    <div class="user-avatar-sm">
                        <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
