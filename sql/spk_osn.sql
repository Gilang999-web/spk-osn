-- =============================================
-- SPK OSN - Database Schema
-- =============================================

CREATE DATABASE IF NOT EXISTS spk_osn;
USE spk_osn;

-- =============================================
-- Tabel 1: tb_users
-- =============================================
CREATE TABLE IF NOT EXISTS tb_users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin (password: admin123 — jalankan setup.php untuk regenerate hash)
INSERT INTO tb_users (username, password, nama) VALUES 
('admin', 'y$Ftn3wLEvMEUvLaayI6iHDe5s6gaBd0ulxyN2ay2xiA5hPejiIlblO', 'Admin Utama')
ON DUPLICATE KEY UPDATE username=username;

-- =============================================
-- Tabel 2: tb_kriteria
-- =============================================
CREATE TABLE IF NOT EXISTS tb_kriteria (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    kode VARCHAR(5) UNIQUE NOT NULL,
    nama_kriteria VARCHAR(100) NOT NULL,
    jenis ENUM('Benefit','Cost') NOT NULL DEFAULT 'Benefit',
    bobot DECIMAL(10,6) DEFAULT NULL
);

-- Seed 5 kriteria tetap
INSERT INTO tb_kriteria (kode, nama_kriteria, jenis) VALUES
('C1', 'Nilai Tes Seleksi', 'Benefit'),
('C2', 'Nilai Rapor', 'Benefit'),
('C3', 'Pengalaman Olimpiade', 'Benefit'),
('C4', 'Keaktifan Siswa', 'Benefit'),
('C5', 'Minat Belajar', 'Benefit')
ON DUPLICATE KEY UPDATE kode=kode;

-- =============================================
-- Tabel 3: tb_alternatif
-- =============================================
CREATE TABLE IF NOT EXISTS tb_alternatif (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama_siswa VARCHAR(100) NOT NULL,
    kelas VARCHAR(10) NOT NULL,
    bidang_osn ENUM('IPA','IPS','Matematika') NOT NULL
);

-- =============================================
-- Tabel 4: tb_penilaian
-- =============================================
CREATE TABLE IF NOT EXISTS tb_penilaian (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    id_alternatif INT(11) NOT NULL,
    id_kriteria INT(11) NOT NULL,
    nilai DECIMAL(10,4) NOT NULL,
    FOREIGN KEY (id_alternatif) REFERENCES tb_alternatif(id) ON DELETE CASCADE,
    FOREIGN KEY (id_kriteria) REFERENCES tb_kriteria(id) ON DELETE CASCADE,
    UNIQUE KEY unique_penilaian (id_alternatif, id_kriteria)
);

-- =============================================
-- Tabel 5: tb_perbandingan
-- =============================================
CREATE TABLE IF NOT EXISTS tb_perbandingan (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    id_kriteria_1 INT(11) NOT NULL,
    id_kriteria_2 INT(11) NOT NULL,
    nilai DECIMAL(15,10) NOT NULL,
    FOREIGN KEY (id_kriteria_1) REFERENCES tb_kriteria(id) ON DELETE CASCADE,
    FOREIGN KEY (id_kriteria_2) REFERENCES tb_kriteria(id) ON DELETE CASCADE,
    UNIQUE KEY unique_perbandingan (id_kriteria_1, id_kriteria_2)
);

-- =============================================
-- Tabel 6: tb_hasil
-- =============================================
CREATE TABLE IF NOT EXISTS tb_hasil (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    id_alternatif INT(11) NOT NULL,
    bidang_osn ENUM('IPA','IPS','Matematika') NOT NULL,
    nilai_preferensi DECIMAL(10,6) NOT NULL,
    ranking INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_alternatif) REFERENCES tb_alternatif(id) ON DELETE CASCADE
);
