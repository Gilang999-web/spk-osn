<?php

class AlternatifModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Ambil semua alternatif, urut berdasarkan nama
     */
    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM tb_alternatif ORDER BY bidang_osn, nama_siswa ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil alternatif berdasarkan bidang OSN
     */
    public function getByBidang($bidang) {
        $stmt = $this->conn->prepare("SELECT * FROM tb_alternatif WHERE bidang_osn = ? ORDER BY nama_siswa ASC");
        $stmt->execute([$bidang]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil satu alternatif berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM tb_alternatif WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tambah alternatif baru
     */
    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO tb_alternatif (nama_siswa, kelas, bidang_osn) VALUES (?, ?, ?)");
        return $stmt->execute([$data['nama_siswa'], $data['kelas'], $data['bidang_osn']]);
    }

    /**
     * Update alternatif
     */
    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE tb_alternatif SET nama_siswa = ?, kelas = ?, bidang_osn = ? WHERE id = ?");
        return $stmt->execute([$data['nama_siswa'], $data['kelas'], $data['bidang_osn'], $id]);
    }

    /**
     * Hapus alternatif
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM tb_alternatif WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Hitung jumlah total alternatif
     */
    public function count() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM tb_alternatif");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Hitung jumlah alternatif per bidang
     */
    public function countByBidang($bidang) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tb_alternatif WHERE bidang_osn = ?");
        $stmt->execute([$bidang]);
        return (int) $stmt->fetchColumn();
    }
}
?>
