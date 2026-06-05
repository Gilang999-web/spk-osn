<?php
session_start();
require_once 'config/database.php';

// Cek parameter page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Array halaman yang tidak butuh login
$public_pages = ['login'];

// Middleware cek login
if (!isset($_SESSION['user_id']) && !in_array($page, $public_pages)) {
    // Jika belum login dan mencoba akses halaman private, arahkan ke login
    header("Location: ?page=login");
    exit;
}

// Router sederhana
switch ($page) {
    case 'login':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController($conn);
        $auth->login();
        break;
        
    case 'logout':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController($conn);
        $auth->logout();
        break;
        
    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $dashboard = new DashboardController($conn);
        $dashboard->index();
        break;
        
    case 'kriteria':
        require_once 'controllers/KriteriaController.php';
        $controller = new KriteriaController($conn);
        $action = $_GET['action'] ?? 'index';
        $id     = $_GET['id'] ?? null;

        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit($id);
                break;
            case 'update':
                $controller->update($id);
                break;
            case 'delete':
                $controller->delete($id);
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'alternatif':
        require_once 'controllers/AlternatifController.php';
        $controller = new AlternatifController($conn);
        $action = $_GET['action'] ?? 'index';
        $id     = $_GET['id'] ?? null;

        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit($id);
                break;
            case 'update':
                $controller->update($id);
                break;
            case 'delete':
                $controller->delete($id);
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'penilaian':
        require_once 'controllers/PenilaianController.php';
        $controller = new PenilaianController($conn);
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'store':
                $controller->store();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'ahp':
        require_once 'controllers/AhpController.php';
        $controller = new AhpController($conn);
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'store':
                $controller->store();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'ahp_hasil':
        require_once 'controllers/AhpController.php';
        $controller = new AhpController($conn);
        $controller->hasil();
        break;

    case 'saw':
        require_once 'controllers/SawController.php';
        $controller = new SawController($conn);
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'hitung':
                $controller->hitung();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'hasil':
        require_once 'controllers/SawController.php';
        $controller = new SawController($conn);
        $controller->ringkasan();
        break;

    default:
        // Halaman 404
        echo "<h1>404 Not Found</h1>";
        break;
}
?>
