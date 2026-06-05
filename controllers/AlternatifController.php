<?php
require_once 'models/AlternatifModel.php';

class AlternatifController {
    private $model;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->model = new AlternatifModel($conn);
    }

    /**
     * Halaman daftar alternatif (siswa) dengan filter bidang
     */
    public function index() {
        $data = [];

        // Filter bidang dari query string
        $filter_bidang = $_GET['bidang'] ?? '';
        $data['filter_bidang'] = $filter_bidang;

        // Ambil data sesuai filter
        if (!empty($filter_bidang) && in_array($filter_bidang, ['IPA', 'IPS', 'Matematika'])) {
            $data['alternatif'] = $this->model->getByBidang($filter_bidang);
        } else {
            $data['alternatif'] = $this->model->getAll();
            $data['filter_bidang'] = ''; // reset jika filter tidak valid
        }

        // Hitung per bidang untuk badge
        $data['count_ipa'] = $this->model->countByBidang('IPA');
        $data['count_ips'] = $this->model->countByBidang('IPS');
        $data['count_mtk'] = $this->model->countByBidang('Matematika');
        $data['count_all'] = $this->model->count();

        // Flash message
        $data['success'] = $_SESSION['flash_success'] ?? '';
        $data['error']   = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require_once 'views/alternatif/index.php';
    }

    /**
     * Tampilkan form tambah
     */
    public function create() {
        $data = ['mode' => 'create', 'alternatif' => null];
        $data['error'] = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_error']);
        require_once 'views/alternatif/form.php';
    }

    /**
     * Proses simpan alternatif baru
     */
    public function store() {
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $tingkat    = trim($_POST['tingkat_kelas'] ?? '');
        $rombel     = trim($_POST['rombel'] ?? '');
        $bidang_osn = $_POST['bidang_osn'] ?? '';
        $kelas      = trim($tingkat . ' ' . $rombel);

        // Validasi
        if (empty($nama_siswa) || empty($tingkat) || empty($rombel) || empty($bidang_osn)) {
            $_SESSION['flash_error'] = 'Semua field harus diisi!';
            header("Location: ?page=alternatif&action=create");
            exit;
        }

        if (!in_array($bidang_osn, ['IPA', 'IPS', 'Matematika'])) {
            $_SESSION['flash_error'] = 'Bidang OSN tidak valid!';
            header("Location: ?page=alternatif&action=create");
            exit;
        }

        if (!in_array($tingkat, ['VII', 'VIII'])) {
            $_SESSION['flash_error'] = 'Tingkat kelas harus VII atau VIII!';
            header("Location: ?page=alternatif&action=create");
            exit;
        }

        if (!in_array($rombel, ['A','B','C','D','E','F','G','H','I','J','K'])) {
            $_SESSION['flash_error'] = 'Rombel tidak valid!';
            header("Location: ?page=alternatif&action=create");
            exit;
        }

        $this->model->create([
            'nama_siswa' => $nama_siswa,
            'kelas'      => $kelas,
            'bidang_osn' => $bidang_osn,
        ]);

        $_SESSION['flash_success'] = 'Data siswa "' . $nama_siswa . '" berhasil ditambahkan!';
        header("Location: ?page=alternatif");
        exit;
    }

    /**
     * Tampilkan form edit
     */
    public function edit($id) {
        $alternatif = $this->model->getById($id);
        if (!$alternatif) {
            $_SESSION['flash_error'] = 'Data siswa tidak ditemukan!';
            header("Location: ?page=alternatif");
            exit;
        }

        $data = ['mode' => 'edit', 'alternatif' => $alternatif];
        $data['error'] = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_error']);
        require_once 'views/alternatif/form.php';
    }

    /**
     * Proses update alternatif
     */
    public function update($id) {
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $tingkat    = trim($_POST['tingkat_kelas'] ?? '');
        $rombel     = trim($_POST['rombel'] ?? '');
        $bidang_osn = $_POST['bidang_osn'] ?? '';
        $kelas      = trim($tingkat . ' ' . $rombel);

        // Validasi
        if (empty($nama_siswa) || empty($tingkat) || empty($rombel) || empty($bidang_osn)) {
            $_SESSION['flash_error'] = 'Semua field harus diisi!';
            header("Location: ?page=alternatif&action=edit&id=$id");
            exit;
        }

        if (!in_array($bidang_osn, ['IPA', 'IPS', 'Matematika'])) {
            $_SESSION['flash_error'] = 'Bidang OSN tidak valid!';
            header("Location: ?page=alternatif&action=edit&id=$id");
            exit;
        }

        if (!in_array($tingkat, ['VII', 'VIII'])) {
            $_SESSION['flash_error'] = 'Tingkat kelas harus VII atau VIII!';
            header("Location: ?page=alternatif&action=edit&id=$id");
            exit;
        }

        if (!in_array($rombel, ['A','B','C','D','E','F','G','H','I','J','K'])) {
            $_SESSION['flash_error'] = 'Rombel tidak valid!';
            header("Location: ?page=alternatif&action=edit&id=$id");
            exit;
        }

        $this->model->update($id, [
            'nama_siswa' => $nama_siswa,
            'kelas'      => $kelas,
            'bidang_osn' => $bidang_osn,
        ]);

        $_SESSION['flash_success'] = 'Data siswa "' . $nama_siswa . '" berhasil diperbarui!';
        header("Location: ?page=alternatif");
        exit;
    }

    /**
     * Hapus alternatif
     */
    public function delete($id) {
        $alternatif = $this->model->getById($id);
        if (!$alternatif) {
            $_SESSION['flash_error'] = 'Data siswa tidak ditemukan!';
            header("Location: ?page=alternatif");
            exit;
        }

        $this->model->delete($id);
        $_SESSION['flash_success'] = 'Data siswa "' . $alternatif['nama_siswa'] . '" berhasil dihapus!';
        header("Location: ?page=alternatif");
        exit;
    }
}
?>
