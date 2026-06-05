<?php

class AhpModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Ambil semua data perbandingan berpasangan
     * Mengembalikan array associative: [id_kriteria_1][id_kriteria_2] => nilai
     */
    public function getPerbandingan() {
        $stmt = $this->conn->query("SELECT * FROM tb_perbandingan");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $matriks = [];
        foreach ($rows as $row) {
            $matriks[$row['id_kriteria_1']][$row['id_kriteria_2']] = $row['nilai'];
        }
        return $matriks;
    }

    /**
     * Simpan atau update satu sel perbandingan
     */
    public function savePerbandingan($id_kriteria_1, $id_kriteria_2, $nilai) {
        $stmt = $this->conn->prepare("
            INSERT INTO tb_perbandingan (id_kriteria_1, id_kriteria_2, nilai)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)
        ");
        return $stmt->execute([$id_kriteria_1, $id_kriteria_2, $nilai]);
    }

    /**
     * Hapus semua data perbandingan (reset matriks)
     */
    public function clearPerbandingan() {
        return $this->conn->exec("DELETE FROM tb_perbandingan");
    }

    /**
     * Cek apakah data perbandingan sudah terisi lengkap (25 sel untuk 5×5)
     */
    public function hasPerbandingan() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM tb_perbandingan");
        return (int) $stmt->fetchColumn() > 0;
    }
}
?>
