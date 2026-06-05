<?php

class PenilaianModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Ambil nilai berdasarkan satu alternatif
     * Mengembalikan array associative: id_kriteria => nilai
     */
    public function getNilaiBySiswa($id_alternatif) {
        $stmt = $this->conn->prepare("SELECT id_kriteria, nilai FROM tb_penilaian WHERE id_alternatif = ?");
        $stmt->execute([$id_alternatif]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $nilai = [];
        foreach ($result as $row) {
            $nilai[$row['id_kriteria']] = $row['nilai'];
        }
        return $nilai;
    }

    /**
     * Simpan atau update nilai
     */
    public function saveNilai($id_alternatif, $id_kriteria, $nilai) {
        $stmt = $this->conn->prepare("
            INSERT INTO tb_penilaian (id_alternatif, id_kriteria, nilai) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)
        ");
        return $stmt->execute([$id_alternatif, $id_kriteria, $nilai]);
    }
}
?>
