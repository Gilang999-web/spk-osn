<?php

require_once 'models/AlternatifModel.php';
require_once 'models/KriteriaModel.php';
require_once 'models/PenilaianModel.php';

class PenilaianController {
    private $alternatifModel;
    private $kriteriaModel;
    private $penilaianModel;

    public function __construct($db) {
        $this->alternatifModel = new AlternatifModel($db);
        $this->kriteriaModel = new KriteriaModel($db);
        $this->penilaianModel = new PenilaianModel($db);
    }

    public function index() {
        $bidang_osn = isset($_GET['bidang_osn']) ? $_GET['bidang_osn'] : '';
        
        // Ambil semua kriteria untuk header tabel
        $kriteria = $this->kriteriaModel->getAll();
        
        $siswa = [];
        $nilai_siswa = []; // Array 2D: [id_siswa][id_kriteria] = nilai

        if (!empty($bidang_osn)) {
            // Ambil data siswa berdasarkan bidang
            $siswa = $this->alternatifModel->getByBidang($bidang_osn);
            
            // Ambil nilai setiap siswa
            foreach ($siswa as $s) {
                $nilai_siswa[$s['id']] = $this->penilaianModel->getNilaiBySiswa($s['id']);
            }
        }

        require_once 'views/layouts/header.php';
        require_once 'views/penilaian/index.php';
        require_once 'views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $bidang_osn = $_POST['bidang_osn'];
            $nilai_input = $_POST['nilai'] ?? []; // Array: nilai[id_siswa][id_kriteria]
            
            try {
                // Simpan setiap nilai yang dikirim
                foreach ($nilai_input as $id_alternatif => $kriteria_nilai) {
                    foreach ($kriteria_nilai as $id_kriteria => $nilai) {
                        // Hanya simpan jika nilai tidak kosong
                        if ($nilai !== '') {
                            $this->penilaianModel->saveNilai($id_alternatif, $id_kriteria, $nilai);
                        }
                    }
                }
                
                $_SESSION['success'] = "Data penilaian berhasil disimpan!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
            }
            
            header("Location: ?page=penilaian&bidang_osn=" . urlencode($bidang_osn));
            exit;
        }
    }
}
?>
