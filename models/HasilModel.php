<?php

class HasilModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Simpan satu baris hasil ranking
     */
    public function saveHasil($id_alternatif, $bidang_osn, $nilai_preferensi, $ranking) {
        $stmt = $this->conn->prepare("
            INSERT INTO tb_hasil (id_alternatif, bidang_osn, nilai_preferensi, ranking, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$id_alternatif, $bidang_osn, $nilai_preferensi, $ranking]);
    }

    /**
     * Hapus semua hasil per bidang (sebelum recalculate)
     */
    public function clearByBidang($bidang_osn) {
        $stmt = $this->conn->prepare("DELETE FROM tb_hasil WHERE bidang_osn = ?");
        return $stmt->execute([$bidang_osn]);
    }

    /**
     * Ambil hasil ranking per bidang (JOIN dengan data siswa)
     */
    public function getByBidang($bidang_osn) {
        $stmt = $this->conn->prepare("
            SELECT h.*, a.nama_siswa, a.kelas
            FROM tb_hasil h
            JOIN tb_alternatif a ON h.id_alternatif = a.id
            WHERE h.bidang_osn = ?
            ORDER BY h.ranking ASC
        ");
        $stmt->execute([$bidang_osn]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil Top 5 per bidang
     */
    public function getTop5ByBidang($bidang_osn) {
        $stmt = $this->conn->prepare("
            SELECT h.*, a.nama_siswa, a.kelas
            FROM tb_hasil h
            JOIN tb_alternatif a ON h.id_alternatif = a.id
            WHERE h.bidang_osn = ?
            ORDER BY h.ranking ASC
            LIMIT 5
        ");
        $stmt->execute([$bidang_osn]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cek apakah sudah ada hasil untuk bidang tertentu
     */
    public function hasilExists($bidang_osn) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tb_hasil WHERE bidang_osn = ?");
        $stmt->execute([$bidang_osn]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Ambil waktu perhitungan terakhir per bidang
     */
    public function getLastCalculated($bidang_osn) {
        $stmt = $this->conn->prepare("SELECT MAX(created_at) FROM tb_hasil WHERE bidang_osn = ?");
        $stmt->execute([$bidang_osn]);
        return $stmt->fetchColumn();
    }

    /**
     * Hitung total hasil di semua bidang
     */
    public function countAll() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM tb_hasil");
        return (int) $stmt->fetchColumn();
    }
}
?>
