# 📄 Software Requirements Specification (SRS)
## SPK Penentuan Calon Peserta OSN — SMP Negeri 1 Manonjaya
### Metode Hybrid AHP-SAW | PHP Native MVC | MySQL

**Versi**: 1.0
**Tanggal**: 28 Mei 2026
**Penulis**: Gilang Anugrah (227007097)

---

## 1. Gambaran Umum Sistem

### 1.1 Tujuan Sistem
Membangun Sistem Pendukung Keputusan (SPK) berbasis web untuk menentukan **5 calon peserta OSN terbaik** pada masing-masing bidang (IPA, IPS, Matematika) di SMP Negeri 1 Manonjaya menggunakan metode **Hybrid AHP-SAW**.

### 1.2 Ruang Lingkup
- Sistem digunakan oleh **Admin** (guru pembina / panitia seleksi)
- Data alternatif: **65 siswa** kelas VII dan VIII (23 IPA, 20 IPS, 22 Matematika)
- Proses: **1 AHP** (pembobotan kriteria) → **3 SAW** terpisah (perangkingan per bidang)
- Output: Rekomendasi 5 siswa terbaik per bidang OSN

### 1.3 Tech Stack

| Komponen | Teknologi |
|---|---|
| Bahasa Pemrograman | PHP Native (MVC Pattern) |
| Database | MySQL |
| Web Server | Apache (via **Laragon**) |
| Frontend | HTML5, CSS3, JavaScript |
| CSS Framework | Bootstrap 5 |
| Icon | Bootstrap Icons / Font Awesome |
| Font | Google Fonts (Inter) |
| Export PDF | DomPDF / browser print |
| Environment | Laragon (Windows) |

### 1.4 Arsitektur Sistem — MVC

```
Browser (User)
    │
    ▼
index.php (Router)
    │
    ├──→ Controller (Logika bisnis)
    │        │
    │        ├──→ Model (Query database)
    │        │        │
    │        │        └──→ MySQL Database
    │        │
    │        └──→ View (Tampilan HTML)
    │                 │
    └─────────────────┘
              │
              ▼
      Browser menampilkan halaman
```

### 1.5 Aktor Sistem

| Aktor | Deskripsi | Hak Akses |
|---|---|---|
| **Admin** | Guru pembina / panitia seleksi OSN | Full access: CRUD data, proses hitung, cetak laporan |

> [!NOTE]
> Sistem hanya memiliki 1 aktor (Admin). Tidak ada multi-role (siswa tidak login ke sistem). Kuesioner minat belajar diisi secara offline oleh siswa, hasilnya direkap oleh admin ke dalam sistem.

---

## 2. Kebutuhan Fungsional

### 2.1 Daftar Fitur

| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F-01 | Login | Admin masuk ke sistem menggunakan username & password | Wajib |
| F-02 | Logout | Admin keluar dari sistem | Wajib |
| F-03 | Dashboard | Menampilkan ringkasan data (jumlah siswa, kriteria, status perhitungan) | Wajib |
| F-04 | CRUD Kriteria | Mengelola data 5 kriteria (C1-C5) beserta jenis (Benefit/Cost) | Wajib |
| F-05 | CRUD Alternatif | Mengelola data siswa per bidang OSN (IPA, IPS, Matematika) | Wajib |
| F-06 | Input Penilaian | Menginput nilai setiap siswa pada setiap kriteria (C1-C5) | Wajib |
| F-07 | Input Matriks AHP | Menginput matriks perbandingan berpasangan antar kriteria | Wajib |
| F-08 | Hitung Bobot AHP | Menghitung normalisasi, eigenvector, CI, CR → menghasilkan bobot W1-W5 | Wajib |
| F-09 | Hitung SAW | Menghitung normalisasi matriks keputusan, nilai Vi, dan ranking per bidang | Wajib |
| F-10 | Hasil Ranking | Menampilkan perangkingan siswa per bidang OSN (Top 5) | Wajib |
| F-11 | Cetak Laporan | Mencetak/export hasil ranking ke PDF | Wajib |

### 2.2 Detail Fitur

#### F-01: Login
```
Input   : username, password
Proses  : Validasi ke tabel tb_users
Output  : - Berhasil → redirect ke Dashboard
          - Gagal → tampilkan pesan error
Session : Simpan user_id dan nama di session
```

#### F-04: CRUD Kriteria
```
Data    : kode (C1-C5), nama_kriteria, jenis (Benefit/Cost)
Catatan : 
  - 5 kriteria sudah fix (C1-C5), jadi fitur "Tambah" opsional
  - Bobot TIDAK diinput manual, melainkan dihitung oleh AHP (F-08)
  - Semua kriteria berjenis Benefit
```

#### F-05: CRUD Alternatif (Siswa)
```
Data    : nama_siswa, kelas (VII/VIII), bidang_osn (IPA/IPS/Matematika)
Filter  : Admin bisa filter tampilan berdasarkan bidang OSN
Catatan : Total 65 siswa (23 IPA, 20 IPS, 22 Matematika)
```

#### F-06: Input Penilaian
```
Data    : nilai setiap siswa untuk C1-C5
Flow    : 
  1. Admin pilih bidang OSN (IPA/IPS/MTK)
  2. Sistem tampilkan daftar siswa di bidang tersebut
  3. Admin input nilai C1-C5 untuk setiap siswa
  4. Simpan ke tb_penilaian

Detail kriteria:
  C1 - Nilai Tes Seleksi    : Angka (0-100)
  C2 - Nilai Rapor           : Angka (0-100) 
  C3 - Pengalaman Olimpiade  : Angka (sesuai skala konversi)
  C4 - Keaktifan Siswa       : Angka (sesuai skala konversi)
  C5 - Minat Belajar         : Angka (total skor kuesioner Likert)
```

#### F-07 & F-08: AHP (Pembobotan Kriteria)
```
Flow:
  1. Admin mengisi matriks perbandingan berpasangan 5×5
     (berdasarkan kuesioner dari Wakasek Kurikulum)
  2. Sistem otomatis menghitung:
     a. Jumlah setiap kolom
     b. Normalisasi matriks (r_ij = a_ij / Σa_ij)
     c. Bobot prioritas / Eigenvector (W_i = rata-rata baris)
     d. λ max (eigenvalue terbesar)
     e. CI = (λmax - n) / (n - 1), dimana n = 5
     f. CR = CI / RI, dimana RI = 1.12 (untuk n=5)
  3. Sistem menampilkan:
     - Matriks perbandingan berpasangan
     - Matriks ternormalisasi
     - Bobot setiap kriteria (W1-W5)
     - Nilai CI dan CR
     - Status: "Konsisten" jika CR ≤ 0.1, "Tidak Konsisten" jika CR > 0.1

Catatan: AHP hanya 1 kali, bobot berlaku untuk semua bidang OSN
```

#### F-09 & F-10: SAW (Perangkingan per Bidang)
```
Flow:
  1. Admin memilih bidang OSN (IPA / IPS / Matematika)
  2. Sistem mengambil:
     - Data penilaian siswa di bidang tersebut
     - Bobot kriteria dari hasil AHP
  3. Sistem menghitung:
     a. Matriks keputusan (X) → nilai asli setiap siswa
     b. Normalisasi (R):
        - Benefit: r_ij = x_ij / max(x_ij)
        - Cost: r_ij = min(x_ij) / x_ij
        - Semua C1-C5 = Benefit
     c. Nilai preferensi: Vi = Σ(Wj × rij)
     d. Ranking berdasarkan Vi (descending)
  4. Sistem menampilkan:
     - Matriks keputusan
     - Matriks ternormalisasi
     - Nilai Vi setiap siswa
     - Ranking (Top 5 ditandai khusus)

Catatan: SAW dijalankan 3 kali TERPISAH (IPA, IPS, Matematika)
         max(x_ij) dicari hanya dari siswa di bidang yang sama
```

#### F-11: Cetak Laporan
```
Output: Laporan hasil ranking per bidang OSN
Isi   : Nama siswa, nilai tiap kriteria, nilai Vi, ranking
Format: PDF / Print browser
```

---

## 3. Kebutuhan Non-Fungsional

| ID | Kebutuhan | Deskripsi |
|---|---|---|
| NF-01 | Berbasis Web | Diakses melalui browser (Chrome/Firefox) |
| NF-02 | User-Friendly | Tampilan sederhana, mudah digunakan tanpa keahlian khusus |
| NF-03 | Responsif | Tampilan menyesuaikan ukuran layar (desktop/tablet) |
| NF-04 | Keamanan | Halaman hanya bisa diakses setelah login (session-based auth) |
| NF-05 | Performa | Proses perhitungan AHP-SAW selesai dalam waktu < 5 detik |
| NF-06 | Integritas Data | Data tersimpan terstruktur di MySQL, mendukung relasi antar tabel |

---

## 4. Struktur Database

### 4.1 Entity Relationship Diagram (ERD)

```
┌──────────────┐       ┌──────────────────┐       ┌──────────────┐
│   tb_users   │       │   tb_kriteria    │       │tb_alternatif │
├──────────────┤       ├──────────────────┤       ├──────────────┤
│ *id          │       │ *id              │       │ *id          │
│  username    │       │  kode            │       │  nama_siswa  │
│  password    │       │  nama_kriteria   │       │  kelas       │
│  nama        │       │  jenis           │       │  bidang_osn  │
└──────────────┘       │  bobot           │       └──────┬───────┘
                       └────────┬─────────┘              │
                                │                        │
                       ┌────────┴────────────────────────┴───────┐
                       │              tb_penilaian               │
                       ├─────────────────────────────────────────┤
                       │ *id                                     │
                       │  id_alternatif (FK → tb_alternatif.id)  │
                       │  id_kriteria (FK → tb_kriteria.id)      │
                       │  nilai                                  │
                       └─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│           tb_perbandingan               │
├─────────────────────────────────────────┤
│ *id                                     │
│  id_kriteria_1 (FK → tb_kriteria.id)    │
│  id_kriteria_2 (FK → tb_kriteria.id)    │
│  nilai                                  │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│              tb_hasil                   │
├─────────────────────────────────────────┤
│ *id                                     │
│  id_alternatif (FK → tb_alternatif.id)  │
│  bidang_osn                             │
│  nilai_preferensi                       │
│  ranking                                │
│  created_at                             │
└─────────────────────────────────────────┘
```

### 4.2 Detail Struktur Tabel

#### Tabel 1: `tb_users`
| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT(11) | PRIMARY KEY, AUTO_INCREMENT | ID unik user |
| username | VARCHAR(50) | UNIQUE, NOT NULL | Username login |
| password | VARCHAR(255) | NOT NULL | Password (di-hash) |
| nama | VARCHAR(100) | NOT NULL | Nama lengkap admin |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |

#### Tabel 2: `tb_kriteria`
| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT(11) | PRIMARY KEY, AUTO_INCREMENT | ID unik kriteria |
| kode | VARCHAR(5) | UNIQUE, NOT NULL | Kode kriteria (C1-C5) |
| nama_kriteria | VARCHAR(100) | NOT NULL | Nama kriteria |
| jenis | ENUM('Benefit','Cost') | NOT NULL, DEFAULT 'Benefit' | Jenis kriteria |
| bobot | DECIMAL(10,6) | DEFAULT NULL | Bobot dari AHP (diisi otomatis oleh sistem) |

#### Tabel 3: `tb_alternatif`
| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT(11) | PRIMARY KEY, AUTO_INCREMENT | ID unik siswa |
| nama_siswa | VARCHAR(100) | NOT NULL | Nama lengkap siswa |
| kelas | VARCHAR(10) | NOT NULL | Kelas siswa (VII/VIII) |
| bidang_osn | ENUM('IPA','IPS','Matematika') | NOT NULL | Bidang OSN yang diikuti |

#### Tabel 4: `tb_penilaian`
| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT(11) | PRIMARY KEY, AUTO_INCREMENT | ID unik penilaian |
| id_alternatif | INT(11) | FOREIGN KEY → tb_alternatif(id) | ID siswa |
| id_kriteria | INT(11) | FOREIGN KEY → tb_kriteria(id) | ID kriteria |
| nilai | DECIMAL(10,4) | NOT NULL | Nilai siswa pada kriteria tersebut |

**Constraint**: UNIQUE(id_alternatif, id_kriteria) — 1 siswa hanya punya 1 nilai per kriteria

#### Tabel 5: `tb_perbandingan`
| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT(11) | PRIMARY KEY, AUTO_INCREMENT | ID unik |
| id_kriteria_1 | INT(11) | FOREIGN KEY → tb_kriteria(id) | Kriteria baris (i) |
| id_kriteria_2 | INT(11) | FOREIGN KEY → tb_kriteria(id) | Kriteria kolom (j) |
| nilai | DECIMAL(10,4) | NOT NULL | Nilai perbandingan (skala Saaty 1-9) |

**Constraint**: UNIQUE(id_kriteria_1, id_kriteria_2)

#### Tabel 6: `tb_hasil`
| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT(11) | PRIMARY KEY, AUTO_INCREMENT | ID unik |
| id_alternatif | INT(11) | FOREIGN KEY → tb_alternatif(id) | ID siswa |
| bidang_osn | ENUM('IPA','IPS','Matematika') | NOT NULL | Bidang OSN |
| nilai_preferensi | DECIMAL(10,6) | NOT NULL | Nilai Vi dari SAW |
| ranking | INT(11) | NOT NULL | Urutan ranking |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu perhitungan |

---

## 5. Alur Perhitungan

### 5.1 Alur AHP (1 kali, berlaku untuk semua bidang)

```
START
  │
  ▼
[1] Input matriks perbandingan berpasangan (5×5)
    - Diagonal utama = 1
    - a_ji = 1 / a_ij (reciprocal)
    - Total input: 10 sel (segitiga atas)
  │
  ▼
[2] Hitung jumlah setiap kolom
    - kolom_j = Σ a_ij untuk i = 1..5
  │
  ▼
[3] Normalisasi matriks
    - r_ij = a_ij / kolom_j
  │
  ▼
[4] Hitung bobot prioritas (Eigenvector)
    - W_i = (Σ r_ij untuk j = 1..5) / 5
    - Hasil: W1, W2, W3, W4, W5
  │
  ▼
[5] Hitung λ max
    - Kalikan matriks asli × vektor bobot
    - λmax = rata-rata dari (hasil_i / W_i)
  │
  ▼
[6] Hitung CI dan CR
    - CI = (λmax - 5) / (5 - 1)
    - CR = CI / 1.12
  │
  ▼
[7] Cek konsistensi
    - CR ≤ 0.1? → Konsisten ✅ → Simpan bobot ke tb_kriteria
    - CR > 0.1? → Tidak konsisten ❌ → Tampilkan peringatan
  │
  ▼
END
```

### 5.2 Alur SAW (dijalankan 3 kali, per bidang OSN)

```
START
  │
  ▼
[1] Pilih bidang OSN (IPA / IPS / Matematika)
  │
  ▼
[2] Ambil data dari database:
    - Siswa di bidang tersebut (tb_alternatif WHERE bidang_osn = X)
    - Nilai penilaian (tb_penilaian)
    - Bobot kriteria dari AHP (tb_kriteria.bobot)
  │
  ▼
[3] Susun matriks keputusan (X)
    - Baris = siswa, Kolom = kriteria
    - Ukuran: n_siswa × 5
  │
  ▼
[4] Normalisasi matriks (R)
    - Untuk setiap kolom j (kriteria):
      - Cari max(x_ij) dari semua siswa di bidang ini
      - r_ij = x_ij / max(x_ij)  [karena semua Benefit]
  │
  ▼
[5] Hitung nilai preferensi (Vi)
    - Untuk setiap siswa i:
      Vi = (W1 × ri1) + (W2 × ri2) + (W3 × ri3) + (W4 × ri4) + (W5 × ri5)
  │
  ▼
[6] Urutkan Vi dari terbesar ke terkecil → Ranking
  │
  ▼
[7] Simpan hasil ke tb_hasil
  │
  ▼
[8] Tampilkan ranking (Top 5 ditandai khusus)
  │
  ▼
END
```

---

## 6. Daftar Halaman Website

| No | Halaman | URL Route | Deskripsi |
|---|---|---|---|
| 1 | Login | `?page=login` | Form login (username + password) |
| 2 | Dashboard | `?page=dashboard` | Ringkasan jumlah data + status perhitungan |
| 3 | Data Kriteria | `?page=kriteria` | Tabel daftar kriteria + form tambah/edit |
| 4 | Data Alternatif | `?page=alternatif` | Tabel daftar siswa + filter bidang OSN + form tambah/edit |
| 5 | Input Penilaian | `?page=penilaian` | Form input nilai C1-C5 per siswa (filter per bidang) |
| 6 | AHP - Input Matriks | `?page=ahp` | Form input matriks perbandingan berpasangan |
| 7 | AHP - Hasil | `?page=ahp_hasil` | Tampilkan matriks normalisasi, bobot, CI, CR |
| 8 | SAW - Perhitungan | `?page=saw` | Pilih bidang → tampilkan normalisasi + Vi + ranking |
| 9 | Hasil Ranking | `?page=hasil` | Tampilkan Top 5 per bidang OSN |
| 10 | Cetak Laporan | `?page=laporan` | Halaman cetak PDF hasil ranking |

---

## 7. Struktur Folder Project

```
📁 spk-osn/                          ← Root project (di htdocs Laragon)
│
├── 📄 index.php                      ← Entry point + Router
├── 📄 .htaccess                      ← URL rewrite (opsional)
│
├── 📁 config/
│   └── 📄 database.php               ← Koneksi MySQL (host, user, pass, db)
│
├── 📁 models/
│   ├── 📄 UserModel.php              ← Query: login, get user
│   ├── 📄 KriteriaModel.php          ← Query: CRUD kriteria, update bobot
│   ├── 📄 AlternatifModel.php        ← Query: CRUD siswa, filter bidang
│   ├── 📄 PenilaianModel.php         ← Query: CRUD nilai, get per bidang
│   ├── 📄 AhpModel.php               ← Query: simpan/ambil matriks perbandingan
│   └── 📄 HasilModel.php             ← Query: simpan/ambil hasil ranking
│
├── 📁 controllers/
│   ├── 📄 AuthController.php         ← Logika login, logout, cek session
│   ├── 📄 DashboardController.php    ← Logika hitung ringkasan data
│   ├── 📄 KriteriaController.php     ← Logika CRUD kriteria
│   ├── 📄 AlternatifController.php   ← Logika CRUD siswa
│   ├── 📄 PenilaianController.php    ← Logika input/edit nilai
│   ├── 📄 AhpController.php          ← Logika hitung AHP (normalisasi, bobot, CR)
│   ├── 📄 SawController.php          ← Logika hitung SAW (normalisasi, Vi, ranking)
│   └── 📄 LaporanController.php      ← Logika generate laporan
│
├── 📁 views/
│   ├── 📁 layouts/
│   │   ├── 📄 header.php             ← <head>, navbar, sidebar
│   │   └── 📄 footer.php             ← Footer, scripts
│   ├── 📄 login.php
│   ├── 📄 dashboard.php
│   ├── 📄 kriteria/
│   │   ├── 📄 index.php              ← Tabel daftar kriteria
│   │   └── 📄 form.php               ← Form tambah/edit
│   ├── 📄 alternatif/
│   │   ├── 📄 index.php              ← Tabel daftar siswa
│   │   └── 📄 form.php               ← Form tambah/edit
│   ├── 📄 penilaian/
│   │   └── 📄 index.php              ← Form input nilai
│   ├── 📄 ahp/
│   │   ├── 📄 input.php              ← Form matriks perbandingan
│   │   └── 📄 hasil.php              ← Tampilkan bobot + CR
│   ├── 📄 saw/
│   │   └── 📄 index.php              ← Tampilkan normalisasi + Vi + ranking
│   ├── 📄 hasil/
│   │   └── 📄 index.php              ← Tampilkan Top 5 per bidang
│   └── 📄 laporan/
│       └── 📄 cetak.php              ← Template cetak PDF
│
├── 📁 assets/
│   ├── 📁 css/
│   │   └── 📄 style.css              ← Custom CSS
│   ├── 📁 js/
│   │   └── 📄 script.js              ← Custom JavaScript
│   └── 📁 img/
│       └── 📄 logo.png               ← Logo sekolah (opsional)
│
└── 📁 sql/
    └── 📄 spk_osn.sql                ← File SQL untuk import database
```

---

## 8. Skala Konversi Kriteria

### C1 — Nilai Tes Seleksi
```
Input langsung: angka 0-100 (nilai asli dari hasil tes)
```

### C2 — Nilai Rapor Mata Pelajaran Terkait
```
Input langsung: angka 0-100 (nilai rapor mata pelajaran sesuai bidang OSN)
- IPA → Nilai rapor IPA
- IPS → Nilai rapor IPS
- Matematika → Nilai rapor Matematika
```

### C3 — Pengalaman Mengikuti Olimpiade
> [!IMPORTANT]
> Skala konversi ini perlu **dikonfirmasi ke dosen pembimbing / Wakasek**. Berikut contoh yang bisa digunakan:

| Pengalaman | Nilai |
|---|---|
| Belum pernah mengikuti olimpiade | 1 |
| Pernah 1 kali mengikuti olimpiade | 2 |
| Pernah 2 kali mengikuti olimpiade | 3 |
| Pernah 3 kali atau lebih | 4 |
| Pernah menjadi perwakilan sekolah / juara | 5 |

### C4 — Keaktifan Siswa
> [!IMPORTANT]
> Skala konversi ini juga perlu **dikonfirmasi**. Berikut contoh:

| Keaktifan | Nilai |
|---|---|
| Kurang aktif | 1 |
| Cukup aktif | 2 |
| Aktif | 3 |
| Sangat aktif | 4 |
| Luar biasa aktif (pengurus OSIS / ketua ekskul) | 5 |

### C5 — Minat Belajar Siswa
```
Sumber   : Kuesioner skala Likert (1-5 per butir)
Konversi : Total skor seluruh butir pertanyaan

- IPA       : 18 butir → Skor total range 18–90
- IPS       : 20 butir → Skor total range 20–100
- Matematika: 20 butir → Skor total range 20–100

Catatan: Skor dari kuesioner berbeda TIDAK dicampur lintas bidang.
         Normalisasi SAW akan menangani perbedaan skala.
```

---

## 9. Sprint Plan

### Sprint 1: Fondasi + Login 🏗️
```
Deliverables:
  ☐ Setup folder project MVC di Laragon
  ☐ Buat config/database.php (koneksi MySQL)
  ☐ Buat file SQL: database + tabel tb_users + data admin default
  ☐ Buat router sederhana (index.php)
  ☐ Buat layout (header.php + footer.php) dengan Bootstrap 5
  ☐ Buat halaman login (view + controller + model)
  ☐ Buat fungsi logout
  ☐ Buat middleware cek session (redirect jika belum login)

Test:
  ☐ Bisa akses halaman login
  ☐ Login dengan data benar → masuk dashboard
  ☐ Login dengan data salah → muncul error
  ☐ Akses halaman lain tanpa login → redirect ke login
```

### Sprint 2: Dashboard 📊
```
Deliverables:
  ☐ Buat halaman dashboard
  ☐ Tampilkan card: jumlah siswa, jumlah kriteria, status AHP
  ☐ Sidebar navigasi ke semua halaman

Test:
  ☐ Dashboard menampilkan data ringkasan
  ☐ Navigasi sidebar berfungsi
```

### Sprint 3: CRUD Kriteria 📋
```
Deliverables:
  ☐ Buat file SQL: tabel tb_kriteria + data awal C1-C5
  ☐ Buat KriteriaModel, KriteriaController, views/kriteria
  ☐ Halaman daftar kriteria (tabel)
  ☐ Form tambah/edit kriteria
  ☐ Fungsi hapus kriteria
  ☐ Kolom bobot tampil tapi readonly (diisi otomatis oleh AHP)

Test:
  ☐ CRUD kriteria berfungsi
  ☐ Data tersimpan di database
```

### Sprint 4: CRUD Alternatif (Siswa) 👥
```
Deliverables:
  ☐ Buat file SQL: tabel tb_alternatif
  ☐ Buat AlternatifModel, AlternatifController, views/alternatif
  ☐ Halaman daftar siswa dengan filter dropdown bidang OSN
  ☐ Form tambah/edit siswa (nama, kelas, bidang_osn)
  ☐ Fungsi hapus siswa

Test:
  ☐ CRUD siswa berfungsi
  ☐ Filter bidang OSN berfungsi
  ☐ Data tersimpan di database
```

### Sprint 5: Input Penilaian ✏️
```
Deliverables:
  ☐ Buat file SQL: tabel tb_penilaian
  ☐ Buat PenilaianModel, PenilaianController, views/penilaian
  ☐ Dropdown pilih bidang OSN → tampilkan tabel siswa
  ☐ Form input nilai C1-C5 untuk setiap siswa (inline/batch)
  ☐ Simpan ke database

Test:
  ☐ Input nilai per siswa berfungsi
  ☐ Edit nilai yang sudah ada berfungsi
  ☐ Data tersimpan dengan benar di tb_penilaian
```

### Sprint 6: Perhitungan AHP ⚖️
```
Deliverables:
  ☐ Buat file SQL: tabel tb_perbandingan
  ☐ Buat AhpModel, AhpController, views/ahp
  ☐ Form input matriks perbandingan (hanya segitiga atas, diagonal=1, bawah=reciprocal)
  ☐ Logika hitung: normalisasi, eigenvector, λmax, CI, CR
  ☐ Halaman hasil: tampilkan matriks, bobot, CR, status konsistensi
  ☐ Simpan bobot ke tb_kriteria.bobot

Test:
  ☐ Input matriks berfungsi
  ☐ Hasil bobot sama dengan perhitungan manual Excel
  ☐ Nilai CR sama dengan perhitungan manual
  ☐ Status konsistensi benar
```

### Sprint 7: Perhitungan SAW + Ranking 🏆
```
Deliverables:
  ☐ Buat file SQL: tabel tb_hasil
  ☐ Buat SawController, HasilModel, views/saw, views/hasil
  ☐ Dropdown pilih bidang OSN
  ☐ Logika: ambil data per bidang → normalisasi → hitung Vi → ranking
  ☐ Tampilkan: matriks keputusan, matriks normalisasi, nilai Vi, ranking
  ☐ Tandai Top 5 siswa
  ☐ Simpan hasil ke tb_hasil

Test:
  ☐ Perhitungan SAW per bidang berfungsi
  ☐ Hasil Vi sama dengan perhitungan manual Excel
  ☐ Ranking benar (Vi terbesar = ranking 1)
  ☐ 3 bidang menghasilkan ranking terpisah
```

### Sprint 8: Cetak Laporan 📄
```
Deliverables:
  ☐ Buat LaporanController, views/laporan/cetak
  ☐ Halaman rekap hasil per bidang (format cetak)
  ☐ Tombol cetak / export PDF
  ☐ Layout cetak: kop surat, tabel ranking, tanda tangan

Test:
  ☐ Halaman cetak tampil rapi
  ☐ Print / PDF berfungsi
```

### Sprint 9: Polish UI 💅
```
Deliverables:
  ☐ Sesuaikan tampilan dengan desain Stitch AI
  ☐ Tambah validasi form (client-side + server-side)
  ☐ Tambah notifikasi sukses/error (alert/toast)
  ☐ Tambah konfirmasi sebelum hapus data
  ☐ Responsive layout
  ☐ Loading indicator saat proses perhitungan

Test:
  ☐ Semua halaman sesuai desain
  ☐ Validasi mencegah input kosong/salah
  ☐ Notifikasi muncul dengan benar
```

### Sprint 10: Testing & Validasi 🧪
```
Deliverables:
  ☐ Blackbox testing semua fitur (buat tabel EP)
  ☐ Validasi AHP: manual vs sistem (WAJIB SAMA)
  ☐ Validasi SAW: manual vs sistem (WAJIB SAMA)
  ☐ Perbaiki semua bug yang ditemukan
  ☐ Screenshot semua halaman untuk Bab 4

Test:
  ☐ Semua fitur lolos blackbox testing
  ☐ Semua perhitungan valid (manual = sistem)
  ☐ Tidak ada bug kritis
```

---

## 10. Open Questions

> [!IMPORTANT]
> Hal-hal berikut perlu dikonfirmasi sebelum atau selama pengerjaan:

1. **Skala konversi C3 (Pengalaman Olimpiade) dan C4 (Keaktifan Siswa)** — Apakah menggunakan skala 1-5 seperti contoh di atas, atau ada skala lain dari pihak sekolah?
2. **Kuesioner C5 sudah disebar?** — Apakah 65 siswa sudah mengisi kuesioner minat belajar dan hasilnya sudah direkap?
3. **Kuesioner AHP sudah diisi?** — Apakah Wakasek Kurikulum sudah mengisi kuesioner perbandingan berpasangan?
4. **Data C1 (Tes Seleksi), C2 (Rapor), C3, C4** — Apakah data-data ini sudah dikumpulkan dari pihak sekolah?
5. **Apakah siswa bisa mendaftar di lebih dari 1 bidang OSN?** — Ini mempengaruhi desain tabel tb_alternatif.
