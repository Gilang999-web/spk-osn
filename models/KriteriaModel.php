<?php

class KriteriaModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Ambil semua kriteria, urut berdasarkan kode
     */
    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM tb_kriteria ORDER BY kode ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil satu kriteria berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM tb_kriteria WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tambah kriteria baru
     */
    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO tb_kriteria (kode, nama_kriteria, jenis) VALUES (?, ?, ?)");
        return $stmt->execute([$data['kode'], $data['nama_kriteria'], $data['jenis']]);
    }

    /**
     * Update kriteria (kode, nama, jenis — bobot TIDAK diubah manual)
     */
    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE tb_kriteria SET kode = ?, nama_kriteria = ?, jenis = ? WHERE id = ?");
        return $stmt->execute([$data['kode'], $data['nama_kriteria'], $data['jenis'], $id]);
    }

    /**
     * Hapus kriteria
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM tb_kriteria WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Update bobot dari hasil AHP
     */
    public function updateBobot($id, $bobot) {
        $stmt = $this->conn->prepare("UPDATE tb_kriteria SET bobot = ? WHERE id = ?");
        return $stmt->execute([$bobot, $id]);
    }

    /**
     * Hitung jumlah kriteria
     */
    public function count() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM tb_kriteria");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Cek apakah kode sudah dipakai (untuk validasi unik)
     */
    public function isKodeExists($kode, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tb_kriteria WHERE kode = ? AND id != ?");
            $stmt->execute([$kode, $excludeId]);
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tb_kriteria WHERE kode = ?");
            $stmt->execute([$kode]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }
}
?>
