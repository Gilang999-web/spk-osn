<?php

require_once 'models/KriteriaModel.php';
require_once 'models/AlternatifModel.php';
require_once 'models/PenilaianModel.php';
require_once 'models/HasilModel.php';

class SawController {
    private $conn;
    private $kriteriaModel;
    private $alternatifModel;
    private $penilaianModel;
    private $hasilModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->kriteriaModel = new KriteriaModel($db);
        $this->alternatifModel = new AlternatifModel($db);
        $this->penilaianModel = new PenilaianModel($db);
        $this->hasilModel = new HasilModel($db);
    }

    /**
     * Halaman utama SAW — pilih bidang + tampilkan hasil jika sudah dihitung
     */
    public function index() {
        $data = [];
        $data['kriteria'] = $this->kriteriaModel->getAll();
        $bidang_osn = isset($_GET['bidang_osn']) ? $_GET['bidang_osn'] : '';
        $data['bidang_osn'] = $bidang_osn;

        // Flash message
        $data['success'] = $_SESSION['flash_success'] ?? '';
        $data['error']   = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        // Cek apakah bobot AHP sudah terisi
        $bobot_terisi = true;
        foreach ($data['kriteria'] as $k) {
            if (empty($k['bobot']) || $k['bobot'] <= 0) {
                $bobot_terisi = false;
                break;
            }
        }
        $data['bobot_terisi'] = $bobot_terisi;

        if (!$bobot_terisi) {
            $data['error'] = 'Bobot AHP belum dihitung. Silakan lakukan perhitungan AHP terlebih dahulu.';
        }

        // Jika bidang dipilih, hitung SAW
        if (!empty($bidang_osn) && $bobot_terisi) {
            $this->hitungSaw($data, $bidang_osn);
        }

        require_once 'views/layouts/header.php';
        require_once 'views/saw/index.php';
        require_once 'views/layouts/footer.php';
    }

    /**
     * Proses perhitungan SAW dan simpan hasil
     */
    public function hitung() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?page=saw");
            exit;
        }

        $bidang_osn = $_POST['bidang_osn'] ?? '';
        if (empty($bidang_osn)) {
            $_SESSION['flash_error'] = 'Bidang OSN tidak valid.';
            header("Location: ?page=saw");
            exit;
        }

        $kriteria = $this->kriteriaModel->getAll();

        // Validasi bobot AHP
        foreach ($kriteria as $k) {
            if (empty($k['bobot']) || $k['bobot'] <= 0) {
                $_SESSION['flash_error'] = 'Bobot AHP belum dihitung. Lakukan perhitungan AHP terlebih dahulu.';
                header("Location: ?page=saw&bidang_osn=" . urlencode($bidang_osn));
                exit;
            }
        }

        $siswa = $this->alternatifModel->getByBidang($bidang_osn);
        if (empty($siswa)) {
            $_SESSION['flash_error'] = 'Tidak ada data siswa untuk bidang ' . $bidang_osn . '.';
            header("Location: ?page=saw&bidang_osn=" . urlencode($bidang_osn));
            exit;
        }

        $n = count($siswa);
        $m = count($kriteria);
        $ids_kriteria = array_column($kriteria, 'id');

        // ==============================
        // STEP 1: Bangun Matriks Keputusan (X)
        // ==============================
        $matriks_keputusan = [];
        $penilaian_lengkap = true;

        for ($i = 0; $i < $n; $i++) {
            $nilai = $this->penilaianModel->getNilaiBySiswa($siswa[$i]['id']);
            for ($j = 0; $j < $m; $j++) {
                if (!isset($nilai[$ids_kriteria[$j]]) || $nilai[$ids_kriteria[$j]] === '') {
                    $penilaian_lengkap = false;
                    break 2;
                }
                $matriks_keputusan[$i][$j] = floatval($nilai[$ids_kriteria[$j]]);
            }
        }

        if (!$penilaian_lengkap) {
            $_SESSION['flash_error'] = 'Data penilaian belum lengkap. Pastikan semua siswa bidang ' . $bidang_osn . ' sudah dinilai pada semua kriteria (C1-C5).';
            header("Location: ?page=saw&bidang_osn=" . urlencode($bidang_osn));
            exit;
        }

        // ==============================
        // STEP 2: Normalisasi (R)
        // ==============================
        // Cari max dan min per kolom (kriteria)
        $max_kolom = [];
        $min_kolom = [];
        for ($j = 0; $j < $m; $j++) {
            $kolom_values = [];
            for ($i = 0; $i < $n; $i++) {
                $kolom_values[] = $matriks_keputusan[$i][$j];
            }
            $max_kolom[$j] = max($kolom_values);
            $min_kolom[$j] = min($kolom_values);
        }

        // Normalisasi berdasarkan jenis kriteria
        $matriks_normal = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                if ($kriteria[$j]['jenis'] === 'Benefit') {
                    // Benefit: r_ij = x_ij / max(x_j)
                    $matriks_normal[$i][$j] = ($max_kolom[$j] != 0) 
                        ? $matriks_keputusan[$i][$j] / $max_kolom[$j] 
                        : 0;
                } else {
                    // Cost: r_ij = min(x_j) / x_ij
                    $matriks_normal[$i][$j] = ($matriks_keputusan[$i][$j] != 0) 
                        ? $min_kolom[$j] / $matriks_keputusan[$i][$j] 
                        : 0;
                }
            }
        }

        // ==============================
        // STEP 3: Hitung Nilai Preferensi (Vi)
        // Vi = Σ (W_j × r_ij)
        // ==============================
        $bobot = [];
        for ($j = 0; $j < $m; $j++) {
            $bobot[$j] = floatval($kriteria[$j]['bobot']);
        }

        $vi = [];
        for ($i = 0; $i < $n; $i++) {
            $vi[$i] = 0;
            for ($j = 0; $j < $m; $j++) {
                $vi[$i] += $bobot[$j] * $matriks_normal[$i][$j];
            }
        }

        // ==============================
        // STEP 4: Ranking (descending Vi)
        // ==============================
        // Buat array index + Vi untuk sorting
        $ranking_data = [];
        for ($i = 0; $i < $n; $i++) {
            $ranking_data[] = [
                'index' => $i,
                'id_alternatif' => $siswa[$i]['id'],
                'nama_siswa' => $siswa[$i]['nama_siswa'],
                'kelas' => $siswa[$i]['kelas'],
                'vi' => $vi[$i]
            ];
        }

        // Sort descending by Vi
        usort($ranking_data, function($a, $b) {
            return $b['vi'] <=> $a['vi'];
        });

        // Assign ranking
        for ($i = 0; $i < count($ranking_data); $i++) {
            $ranking_data[$i]['ranking'] = $i + 1;
        }

        // ==============================
        // STEP 5: Simpan ke tb_hasil
        // ==============================
        try {
            // Hapus hasil lama untuk bidang ini
            $this->hasilModel->clearByBidang($bidang_osn);

            // Simpan hasil baru
            foreach ($ranking_data as $rd) {
                $this->hasilModel->saveHasil(
                    $rd['id_alternatif'],
                    $bidang_osn,
                    $rd['vi'],
                    $rd['ranking']
                );
            }

            $_SESSION['flash_success'] = 'Perhitungan SAW bidang ' . $bidang_osn . ' berhasil! Ranking telah disimpan.';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage();
        }

        header("Location: ?page=saw&bidang_osn=" . urlencode($bidang_osn));
        exit;
    }

    /**
     * Hitung SAW untuk ditampilkan di halaman (tanpa menyimpan)
     */
    private function hitungSaw(&$data, $bidang_osn) {
        $kriteria = $data['kriteria'];
        $siswa = $this->alternatifModel->getByBidang($bidang_osn);
        $data['siswa'] = $siswa;

        if (empty($siswa)) {
            $data['error'] = 'Tidak ada data siswa untuk bidang ' . $bidang_osn . '.';
            return;
        }

        $n = count($siswa);
        $m = count($kriteria);
        $ids_kriteria = array_column($kriteria, 'id');

        // Bangun Matriks Keputusan
        $matriks_keputusan = [];
        $penilaian_lengkap = true;

        for ($i = 0; $i < $n; $i++) {
            $nilai = $this->penilaianModel->getNilaiBySiswa($siswa[$i]['id']);
            for ($j = 0; $j < $m; $j++) {
                if (!isset($nilai[$ids_kriteria[$j]]) || $nilai[$ids_kriteria[$j]] === '') {
                    $penilaian_lengkap = false;
                    $matriks_keputusan[$i][$j] = 0;
                } else {
                    $matriks_keputusan[$i][$j] = floatval($nilai[$ids_kriteria[$j]]);
                }
            }
        }

        $data['matriks_keputusan'] = $matriks_keputusan;
        $data['penilaian_lengkap'] = $penilaian_lengkap;

        if (!$penilaian_lengkap) {
            $data['error'] = 'Data penilaian belum lengkap. Pastikan semua siswa bidang ' . $bidang_osn . ' sudah dinilai pada semua kriteria (C1-C5).';
            return;
        }

        // Cari max dan min per kolom
        $max_kolom = [];
        $min_kolom = [];
        for ($j = 0; $j < $m; $j++) {
            $kolom_values = [];
            for ($i = 0; $i < $n; $i++) {
                $kolom_values[] = $matriks_keputusan[$i][$j];
            }
            $max_kolom[$j] = max($kolom_values);
            $min_kolom[$j] = min($kolom_values);
        }
        $data['max_kolom'] = $max_kolom;
        $data['min_kolom'] = $min_kolom;

        // Normalisasi
        $matriks_normal = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                if ($kriteria[$j]['jenis'] === 'Benefit') {
                    $matriks_normal[$i][$j] = ($max_kolom[$j] != 0)
                        ? $matriks_keputusan[$i][$j] / $max_kolom[$j]
                        : 0;
                } else {
                    $matriks_normal[$i][$j] = ($matriks_keputusan[$i][$j] != 0)
                        ? $min_kolom[$j] / $matriks_keputusan[$i][$j]
                        : 0;
                }
            }
        }
        $data['matriks_normal'] = $matriks_normal;

        // Bobot
        $bobot = [];
        for ($j = 0; $j < $m; $j++) {
            $bobot[$j] = floatval($kriteria[$j]['bobot']);
        }
        $data['bobot'] = $bobot;

        // Hitung Vi
        $vi = [];
        $detail_vi = []; // Detail per kriteria untuk tabel
        for ($i = 0; $i < $n; $i++) {
            $vi[$i] = 0;
            $detail_vi[$i] = [];
            for ($j = 0; $j < $m; $j++) {
                $detail_vi[$i][$j] = $bobot[$j] * $matriks_normal[$i][$j];
                $vi[$i] += $detail_vi[$i][$j];
            }
        }
        $data['vi'] = $vi;
        $data['detail_vi'] = $detail_vi;

        // Ranking
        $ranking_data = [];
        for ($i = 0; $i < $n; $i++) {
            $ranking_data[] = [
                'index' => $i,
                'id_alternatif' => $siswa[$i]['id'],
                'nama_siswa' => $siswa[$i]['nama_siswa'],
                'kelas' => $siswa[$i]['kelas'],
                'vi' => $vi[$i]
            ];
        }

        usort($ranking_data, function($a, $b) {
            return $b['vi'] <=> $a['vi'];
        });

        for ($i = 0; $i < count($ranking_data); $i++) {
            $ranking_data[$i]['ranking'] = $i + 1;
        }
        $data['ranking'] = $ranking_data;

        // Cek apakah sudah pernah dihitung & disimpan
        $data['sudah_disimpan'] = $this->hasilModel->hasilExists($bidang_osn);
        $data['last_calculated'] = $this->hasilModel->getLastCalculated($bidang_osn);
    }

    /**
     * Halaman ringkasan ranking semua bidang (Top 5)
     */
    public function ringkasan() {
        $data = [];
        $data['kriteria'] = $this->kriteriaModel->getAll();

        // Flash message
        $data['success'] = $_SESSION['flash_success'] ?? '';
        $data['error']   = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        // Ambil Top 5 per bidang
        $bidang_list = ['IPA', 'IPS', 'Matematika'];
        $data['bidang_list'] = $bidang_list;

        foreach ($bidang_list as $bidang) {
            $data['ranking_' . strtolower($bidang)] = $this->hasilModel->getByBidang($bidang);
            $data['top5_' . strtolower($bidang)] = $this->hasilModel->getTop5ByBidang($bidang);
            $data['has_data_' . strtolower($bidang)] = $this->hasilModel->hasilExists($bidang);
            $data['last_calc_' . strtolower($bidang)] = $this->hasilModel->getLastCalculated($bidang);
        }

        require_once 'views/layouts/header.php';
        require_once 'views/hasil/index.php';
        require_once 'views/layouts/footer.php';
    }
}
?>
