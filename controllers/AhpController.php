<?php

require_once 'models/KriteriaModel.php';
require_once 'models/AhpModel.php';

class AhpController {
    private $kriteriaModel;
    private $ahpModel;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->kriteriaModel = new KriteriaModel($db);
        $this->ahpModel = new AhpModel($db);
    }

    /**
     * Halaman input matriks perbandingan berpasangan
     */
    public function index() {
        $data = [];
        $data['kriteria'] = $this->kriteriaModel->getAll();
        $data['perbandingan'] = $this->ahpModel->getPerbandingan();

        // Flash message
        $data['success'] = $_SESSION['flash_success'] ?? '';
        $data['error']   = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require_once 'views/ahp/input.php';
    }

    /**
     * Simpan matriks perbandingan berpasangan
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?page=ahp");
            exit;
        }

        $kriteria = $this->kriteriaModel->getAll();
        $n = count($kriteria);
        $ids = array_column($kriteria, 'id');

        try {
            // Hapus data lama
            $this->ahpModel->clearPerbandingan();

            // Simpan semua sel matriks (termasuk diagonal dan reciprocal)
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    if ($i == $j) {
                        // Diagonal = 1
                        $this->ahpModel->savePerbandingan($ids[$i], $ids[$j], 1);
                    } elseif ($i < $j) {
                        // Segitiga atas — dari input form
                        $key = 'perbandingan_' . $ids[$i] . '_' . $ids[$j];
                        $nilai = isset($_POST[$key]) ? floatval($_POST[$key]) : 1;
                        
                        if ($nilai <= 0) $nilai = 1; // safety

                        $this->ahpModel->savePerbandingan($ids[$i], $ids[$j], $nilai);
                        // Reciprocal di segitiga bawah
                        $this->ahpModel->savePerbandingan($ids[$j], $ids[$i], 1 / $nilai);
                    }
                }
            }

            $_SESSION['flash_success'] = 'Matriks perbandingan berhasil disimpan!';
            header("Location: ?page=ahp_hasil");
            exit;

        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
            header("Location: ?page=ahp");
            exit;
        }
    }

    /**
     * Halaman hasil perhitungan AHP
     */
    public function hasil() {
        $data = [];
        $kriteria = $this->kriteriaModel->getAll();
        $data['kriteria'] = $kriteria;
        $n = count($kriteria);
        $ids = array_column($kriteria, 'id');

        // Flash message
        $data['success'] = $_SESSION['flash_success'] ?? '';
        $data['error']   = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        // Cek apakah data perbandingan ada
        if (!$this->ahpModel->hasPerbandingan()) {
            $data['error'] = 'Belum ada data matriks perbandingan. Silakan isi terlebih dahulu.';
            $data['has_data'] = false;
            require_once 'views/ahp/hasil.php';
            return;
        }

        $data['has_data'] = true;
        $perbandingan = $this->ahpModel->getPerbandingan();

        // ==============================
        // STEP 1: Bangun Matriks Perbandingan 5×5
        // ==============================
        $matriks = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $matriks[$i][$j] = isset($perbandingan[$ids[$i]][$ids[$j]])
                    ? floatval($perbandingan[$ids[$i]][$ids[$j]])
                    : ($i == $j ? 1 : 0);
            }
        }
        $data['matriks_perbandingan'] = $matriks;

        // ==============================
        // STEP 2: Hitung Jumlah Setiap Kolom
        // ==============================
        $jumlah_kolom = [];
        for ($j = 0; $j < $n; $j++) {
            $jumlah_kolom[$j] = 0;
            for ($i = 0; $i < $n; $i++) {
                $jumlah_kolom[$j] += $matriks[$i][$j];
            }
        }
        $data['jumlah_kolom'] = $jumlah_kolom;

        // ==============================
        // STEP 3: Normalisasi Matriks
        // r_ij = a_ij / jumlah_kolom_j
        // ==============================
        $matriks_normal = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $matriks_normal[$i][$j] = ($jumlah_kolom[$j] != 0)
                    ? $matriks[$i][$j] / $jumlah_kolom[$j]
                    : 0;
            }
        }
        $data['matriks_normal'] = $matriks_normal;

        // ==============================
        // STEP 4: Hitung Jumlah Baris & Prioritas
        // Jumlah = Σ baris normalisasi
        // Prioritas = Jumlah / n
        // ==============================
        $jumlah_baris = [];
        $prioritas = [];
        for ($i = 0; $i < $n; $i++) {
            $jumlah_baris[$i] = 0;
            for ($j = 0; $j < $n; $j++) {
                $jumlah_baris[$i] += $matriks_normal[$i][$j];
            }
            $prioritas[$i] = $jumlah_baris[$i] / $n;
        }
        $data['jumlah_baris_normal'] = $jumlah_baris;
        $data['prioritas'] = $prioritas;

        // ==============================
        // STEP 5: Matriks Penjumlahan Setiap Baris
        // Setiap sel = matriks_perbandingan[i][j] × prioritas[j]
        // Lalu jumlahkan per baris
        // ==============================
        $matriks_penjumlahan = [];
        $jumlah_penjumlahan_baris = [];
        for ($i = 0; $i < $n; $i++) {
            $row_sum = 0;
            for ($j = 0; $j < $n; $j++) {
                $matriks_penjumlahan[$i][$j] = $matriks[$i][$j] * $prioritas[$j];
                $row_sum += $matriks_penjumlahan[$i][$j];
            }
            $jumlah_penjumlahan_baris[$i] = $row_sum;
        }
        $data['matriks_penjumlahan'] = $matriks_penjumlahan;
        $data['jumlah_penjumlahan_baris'] = $jumlah_penjumlahan_baris;

        // ==============================
        // STEP 6: Perhitungan Rasio Konsistensi
        // Hasil = Penjumlahan Setiap Baris / Prioritas
        // ==============================
        $rasio = [];
        for ($i = 0; $i < $n; $i++) {
            $rasio[$i] = ($prioritas[$i] != 0) ? $jumlah_penjumlahan_baris[$i] / $prioritas[$i] : 0;
        }
        $data['rasio'] = $rasio;

        // ==============================
        // STEP 7: λmax, CI, CR
        // λmax = Total Hasil / n
        // ==============================
        $lambda_max = array_sum($rasio) / $n;
        $ci = ($lambda_max - $n) / ($n - 1);
        $ri = 1.12; // RI untuk n = 5
        $cr = ($ri != 0) ? $ci / $ri : 0;
        $data['lambda_max'] = $lambda_max;
        $data['ci'] = $ci;
        $data['ri'] = $ri;
        $data['cr'] = $cr;
        $data['konsisten'] = ($cr <= 0.1);

        // ==============================
        // STEP 8: Simpan Prioritas jika Konsisten
        // ==============================
        if ($data['konsisten']) {
            for ($i = 0; $i < $n; $i++) {
                $this->kriteriaModel->updateBobot($ids[$i], $prioritas[$i]);
            }
        }

        require_once 'views/ahp/hasil.php';
    }
}
?>
