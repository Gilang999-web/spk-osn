<?php
require_once 'models/KriteriaModel.php';

class KriteriaController {
    private $model;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->model = new KriteriaModel($conn);
    }

    /**
     * Halaman daftar kriteria
     */
    public function index() {
        $data = [];
        $data['kriteria'] = $this->model->getAll();

        // Flash message dari session
        $data['success'] = $_SESSION['flash_success'] ?? '';
        $data['error']   = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require_once 'views/kriteria/index.php';
    }

    /**
     * Tampilkan form tambah
     */
    public function create() {
        $data = ['mode' => 'create', 'kriteria' => null];
        $data['error'] = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_error']);
        require_once 'views/kriteria/form.php';
    }

    /**
     * Proses simpan kriteria baru
     */
    public function store() {
        $kode           = trim($_POST['kode'] ?? '');
        $nama_kriteria  = trim($_POST['nama_kriteria'] ?? '');
        $jenis          = $_POST['jenis'] ?? 'Benefit';

        // Validasi
        if (empty($kode) || empty($nama_kriteria)) {
            $_SESSION['flash_error'] = 'Kode dan nama kriteria harus diisi!';
            header("Location: ?page=kriteria&action=create");
            exit;
        }

        if ($this->model->isKodeExists($kode)) {
            $_SESSION['flash_error'] = "Kode \"$kode\" sudah digunakan!";
            header("Location: ?page=kriteria&action=create");
            exit;
        }

        if ($this->model->isNamaExists($nama_kriteria)) {
            $_SESSION['flash_error'] = "Nama kriteria \"$nama_kriteria\" sudah digunakan!";
            header("Location: ?page=kriteria&action=create");
            exit;
        }

        $this->model->create([
            'kode'           => strtoupper($kode),
            'nama_kriteria'  => $nama_kriteria,
            'jenis'          => $jenis,
        ]);

        $_SESSION['flash_success'] = 'Kriteria berhasil ditambahkan!';
        header("Location: ?page=kriteria");
        exit;
    }

    /**
     * Tampilkan form edit
     */
    public function edit($id) {
        $kriteria = $this->model->getById($id);
        if (!$kriteria) {
            $_SESSION['flash_error'] = 'Data kriteria tidak ditemukan!';
            header("Location: ?page=kriteria");
            exit;
        }

        $data = ['mode' => 'edit', 'kriteria' => $kriteria];
        $data['error'] = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_error']);
        require_once 'views/kriteria/form.php';
    }

    /**
     * Proses update kriteria
     */
    public function update($id) {
        $kode           = trim($_POST['kode'] ?? '');
        $nama_kriteria  = trim($_POST['nama_kriteria'] ?? '');
        $jenis          = $_POST['jenis'] ?? 'Benefit';

        // Validasi
        if (empty($kode) || empty($nama_kriteria)) {
            $_SESSION['flash_error'] = 'Kode dan nama kriteria harus diisi!';
            header("Location: ?page=kriteria&action=edit&id=$id");
            exit;
        }

        if ($this->model->isKodeExists($kode, $id)) {
            $_SESSION['flash_error'] = "Kode \"$kode\" sudah digunakan oleh kriteria lain!";
            header("Location: ?page=kriteria&action=edit&id=$id");
            exit;
        }

        if ($this->model->isNamaExists($nama_kriteria, $id)) {
            $_SESSION['flash_error'] = "Nama kriteria \"$nama_kriteria\" sudah digunakan oleh kriteria lain!";
            header("Location: ?page=kriteria&action=edit&id=$id");
            exit;
        }

        $this->model->update($id, [
            'kode'           => strtoupper($kode),
            'nama_kriteria'  => $nama_kriteria,
            'jenis'          => $jenis,
        ]);

        $_SESSION['flash_success'] = 'Kriteria berhasil diperbarui!';
        header("Location: ?page=kriteria");
        exit;
    }

    /**
     * Hapus kriteria
     */
    public function delete($id) {
        $kriteria = $this->model->getById($id);
        if (!$kriteria) {
            $_SESSION['flash_error'] = 'Data kriteria tidak ditemukan!';
            header("Location: ?page=kriteria");
            exit;
        }

        $this->model->delete($id);
        $_SESSION['flash_success'] = 'Kriteria "' . $kriteria['kode'] . '" berhasil dihapus!';
        header("Location: ?page=kriteria");
        exit;
    }

    /**
     * AJAX: Cek duplikat kode / nama kriteria secara real-time
     */
    public function checkDuplicate() {
        header('Content-Type: application/json');

        $field = $_GET['field'] ?? '';
        $value = trim($_GET['value'] ?? '');
        $excludeId = $_GET['exclude_id'] ?? null;

        $exists = false;

        if ($field === 'kode' && !empty($value)) {
            $exists = $this->model->isKodeExists($value, $excludeId);
        } elseif ($field === 'nama_kriteria' && !empty($value)) {
            $exists = $this->model->isNamaExists($value, $excludeId);
        }

        echo json_encode(['exists' => $exists]);
        exit;
    }
}
?>
