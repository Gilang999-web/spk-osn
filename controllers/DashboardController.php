<?php

class DashboardController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        $data = [];

        // --- Total Kriteria ---
        $data['total_kriteria'] = $this->safeCount("SELECT COUNT(*) FROM tb_kriteria");

        // --- Total Alternatif ---
        $data['total_alternatif'] = $this->safeCount("SELECT COUNT(*) FROM tb_alternatif");

        // --- Jumlah per bidang ---
        $data['count_ipa']  = $this->safeCount("SELECT COUNT(*) FROM tb_alternatif WHERE bidang_osn='IPA'");
        $data['count_ips']  = $this->safeCount("SELECT COUNT(*) FROM tb_alternatif WHERE bidang_osn='IPS'");
        $data['count_mtk']  = $this->safeCount("SELECT COUNT(*) FROM tb_alternatif WHERE bidang_osn='Matematika'");

        // --- Status AHP (apakah bobot sudah terisi?) ---
        $bobot_terisi = $this->safeCount("SELECT COUNT(*) FROM tb_kriteria WHERE bobot IS NOT NULL AND bobot > 0");
        $data['ahp_done'] = ($bobot_terisi >= 5);

        // --- Status penilaian (sudah ada data penilaian?) ---
        $data['total_penilaian'] = $this->safeCount("SELECT COUNT(*) FROM tb_penilaian");

        // --- Status hasil (sudah ada ranking?) ---
        $data['total_hasil'] = $this->safeCount("SELECT COUNT(*) FROM tb_hasil");

        // --- Kriteria dengan bobot (untuk tampilan) ---
        try {
            $stmt = $this->conn->query("SELECT kode, nama_kriteria, bobot FROM tb_kriteria ORDER BY kode");
            $data['kriteria'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['kriteria'] = [];
        }

        require_once 'views/dashboard.php';
    }

    private function safeCount($sql) {
        try {
            $stmt = $this->conn->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>
