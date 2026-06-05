# 📐 Kumpulan Diagram — Bab 4.2 Perancangan Sistem
## SPK Penentuan Calon Peserta OSN | Hybrid AHP-SAW

> [!TIP]
> **Cara menggunakan diagram ini:**
> 1. Copy kode Mermaid di bawah
> 2. Buka [mermaid.live](https://mermaid.live) di browser
> 3. Paste kode → diagram langsung muncul
> 4. Klik **Export** → Download sebagai PNG
> 5. Masukkan gambar PNG ke dokumen Word/Skripsi
>
> Atau gunakan kode ini sebagai **panduan** untuk menggambar ulang di draw.io

---

## 1. Use Case Diagram

**Penjelasan**: Menggambarkan interaksi antara aktor (Admin) dengan fitur-fitur utama sistem SPK.

```mermaid
flowchart LR
    Admin((Admin))

    UC1[Login]
    UC2[Kelola Data Kriteria]
    UC3[Kelola Data Alternatif]
    UC4[Input Penilaian]
    UC5[Input Matriks AHP]
    UC6[Hitung Bobot AHP]
    UC7[Hitung SAW]
    UC8[Lihat Hasil Ranking]
    UC9[Cetak Laporan]
    UC10[Logout]

    Admin --- UC1
    Admin --- UC2
    Admin --- UC3
    Admin --- UC4
    Admin --- UC5
    Admin --- UC6
    Admin --- UC7
    Admin --- UC8
    Admin --- UC9
    Admin --- UC10

    UC5 -.->|include| UC6
    UC6 -.->|include| UC7
    UC7 -.->|include| UC8

    subgraph Sistem SPK Penentuan Calon Peserta OSN
        UC1
        UC2
        UC3
        UC4
        UC5
        UC6
        UC7
        UC8
        UC9
        UC10
    end
```

### Deskripsi Use Case:

| No | Use Case | Deskripsi |
|---|---|---|
| UC1 | Login | Admin memasukkan username dan password untuk masuk ke sistem |
| UC2 | Kelola Data Kriteria | Admin dapat melihat, menambah, mengubah, dan menghapus data kriteria penilaian (C1-C5) |
| UC3 | Kelola Data Alternatif | Admin dapat melihat, menambah, mengubah, dan menghapus data siswa calon peserta OSN |
| UC4 | Input Penilaian | Admin memasukkan nilai setiap siswa pada setiap kriteria (C1-C5) |
| UC5 | Input Matriks AHP | Admin memasukkan nilai perbandingan berpasangan antar kriteria berdasarkan kuesioner Wakasek |
| UC6 | Hitung Bobot AHP | Sistem menghitung normalisasi, eigenvector, CI, dan CR untuk menghasilkan bobot kriteria |
| UC7 | Hitung SAW | Sistem menghitung normalisasi matriks keputusan, nilai preferensi (Vi), dan ranking per bidang OSN |
| UC8 | Lihat Hasil Ranking | Admin melihat hasil perangkingan 5 siswa terbaik per bidang OSN (IPA, IPS, Matematika) |
| UC9 | Cetak Laporan | Admin mencetak/export hasil ranking dalam format PDF |
| UC10 | Logout | Admin keluar dari sistem |

---

## 2. Activity Diagram

### 2.1 Activity Diagram — Login

```mermaid
flowchart TD
    A([Start]) --> B[Membuka halaman login]
    B --> C[Memasukkan username dan password]
    C --> D{Validasi data login}
    D -->|Valid| E[Membuat session user]
    E --> F[Menampilkan halaman dashboard]
    F --> G([End])
    D -->|Tidak Valid| H[Menampilkan pesan error]
    H --> C
```

### 2.2 Activity Diagram — Kelola Data Siswa (Alternatif)

```mermaid
flowchart TD
    A([Start]) --> B[Membuka halaman data alternatif]
    B --> C[Menampilkan daftar siswa]
    C --> D{Pilih aksi}
    D -->|Tambah| E[Mengisi form data siswa baru]
    E --> F{Validasi data}
    F -->|Valid| G[Menyimpan data ke database]
    F -->|Tidak Valid| H[Menampilkan pesan error]
    H --> E
    G --> C

    D -->|Edit| I[Mengisi form edit data siswa]
    I --> J{Validasi data}
    J -->|Valid| K[Mengupdate data di database]
    J -->|Tidak Valid| L[Menampilkan pesan error]
    L --> I
    K --> C

    D -->|Hapus| M{Konfirmasi hapus?}
    M -->|Ya| N[Menghapus data dari database]
    M -->|Tidak| C
    N --> C

    D -->|Filter Bidang| O[Filter siswa berdasarkan bidang OSN]
    O --> C

    C --> P([End])
```

### 2.3 Activity Diagram — Input Penilaian

```mermaid
flowchart TD
    A([Start]) --> B[Membuka halaman penilaian]
    B --> C[Memilih bidang OSN]
    C --> D[Menampilkan daftar siswa pada bidang tersebut]
    D --> E[Menginput nilai C1-C5 untuk setiap siswa]
    E --> F{Validasi nilai}
    F -->|Valid| G[Menyimpan nilai ke database]
    F -->|Tidak Valid| H[Menampilkan pesan error]
    H --> E
    G --> I[Menampilkan notifikasi berhasil]
    I --> J([End])
```

### 2.4 Activity Diagram — Proses Perhitungan AHP-SAW

```mermaid
flowchart TD
    A([Start]) --> B[Membuka halaman AHP]
    B --> C[Menginput matriks perbandingan berpasangan 5x5]
    C --> D[Sistem menghitung normalisasi matriks]
    D --> E[Sistem menghitung bobot prioritas - eigenvector]
    E --> F[Sistem menghitung lambda max, CI, dan CR]
    F --> G{CR ≤ 0.1?}
    G -->|Ya - Konsisten| H[Menyimpan bobot ke database]
    G -->|Tidak - Tidak Konsisten| I[Menampilkan peringatan tidak konsisten]
    I --> C
    H --> J[Menampilkan hasil bobot dan CR]
    J --> K[Membuka halaman SAW]
    K --> L[Memilih bidang OSN]
    L --> M[Sistem mengambil data penilaian siswa pada bidang tersebut]
    M --> N[Sistem melakukan normalisasi matriks keputusan]
    N --> O[Sistem menghitung nilai preferensi Vi]
    O --> P[Sistem melakukan perangkingan]
    P --> Q[Menyimpan hasil ke database]
    Q --> R[Menampilkan ranking Top 5]
    R --> S([End])
```

### 2.5 Activity Diagram — Cetak Laporan

```mermaid
flowchart TD
    A([Start]) --> B[Membuka halaman laporan]
    B --> C[Memilih bidang OSN]
    C --> D{Data hasil ranking tersedia?}
    D -->|Ya| E[Menampilkan tabel hasil ranking]
    D -->|Tidak| F[Menampilkan pesan - lakukan perhitungan terlebih dahulu]
    F --> G([End])
    E --> H[Menekan tombol cetak]
    H --> I[Sistem generate halaman cetak / PDF]
    I --> J[Admin mencetak atau menyimpan file]
    J --> G
```

---

## 3. Sequence Diagram

### 3.1 Sequence Diagram — Login

```mermaid
sequenceDiagram
    actor Admin
    participant V as View<br>(login.php)
    participant C as Controller<br>(AuthController)
    participant M as Model<br>(UserModel)
    participant DB as Database<br>(MySQL)

    Admin->>V: Mengakses halaman login
    V-->>Admin: Menampilkan form login
    Admin->>V: Mengisi username & password
    V->>C: Mengirim data login
    C->>M: getUser(username, password)
    M->>DB: SELECT * FROM tb_users WHERE username=? AND password=?
    DB-->>M: Return hasil query
    M-->>C: Return data user / null

    alt Login berhasil
        C->>C: Membuat session
        C-->>V: Redirect ke dashboard
        V-->>Admin: Menampilkan dashboard
    else Login gagal
        C-->>V: Kirim pesan error
        V-->>Admin: Menampilkan pesan error
    end
```

### 3.2 Sequence Diagram — Proses Perhitungan AHP

```mermaid
sequenceDiagram
    actor Admin
    participant V as View<br>(ahp/input.php)
    participant C as Controller<br>(AhpController)
    participant M as Model<br>(AhpModel)
    participant MK as Model<br>(KriteriaModel)
    participant DB as Database<br>(MySQL)

    Admin->>V: Membuka halaman AHP
    V-->>Admin: Menampilkan form matriks perbandingan
    Admin->>V: Mengisi nilai perbandingan berpasangan
    V->>C: Mengirim data matriks

    C->>M: simpanPerbandingan(data_matriks)
    M->>DB: INSERT INTO tb_perbandingan
    DB-->>M: Berhasil disimpan

    C->>C: hitungNormalisasi()
    C->>C: hitungEigenvector()
    C->>C: hitungLambdaMax()
    C->>C: hitungCI()
    C->>C: hitungCR()

    alt CR ≤ 0.1 (Konsisten)
        C->>MK: updateBobot(W1, W2, W3, W4, W5)
        MK->>DB: UPDATE tb_kriteria SET bobot=?
        DB-->>MK: Berhasil diupdate
        C-->>V: Kirim hasil (bobot, CR, status konsisten)
        V-->>Admin: Menampilkan bobot + CR + status konsisten
    else CR > 0.1 (Tidak Konsisten)
        C-->>V: Kirim hasil (CR, status tidak konsisten)
        V-->>Admin: Menampilkan peringatan tidak konsisten
    end
```

### 3.3 Sequence Diagram — Proses Perhitungan SAW

```mermaid
sequenceDiagram
    actor Admin
    participant V as View<br>(saw/index.php)
    participant C as Controller<br>(SawController)
    participant MA as Model<br>(AlternatifModel)
    participant MP as Model<br>(PenilaianModel)
    participant MK as Model<br>(KriteriaModel)
    participant MH as Model<br>(HasilModel)
    participant DB as Database<br>(MySQL)

    Admin->>V: Membuka halaman SAW
    V-->>Admin: Menampilkan dropdown pilih bidang OSN
    Admin->>V: Memilih bidang OSN
    V->>C: Kirim bidang_osn

    C->>MA: getAlternatifByBidang(bidang_osn)
    MA->>DB: SELECT * FROM tb_alternatif WHERE bidang_osn=?
    DB-->>MA: Return data siswa
    MA-->>C: Return daftar siswa

    C->>MP: getPenilaianByBidang(bidang_osn)
    MP->>DB: SELECT * FROM tb_penilaian JOIN tb_alternatif
    DB-->>MP: Return data penilaian
    MP-->>C: Return matriks keputusan

    C->>MK: getBobot()
    MK->>DB: SELECT bobot FROM tb_kriteria
    DB-->>MK: Return bobot W1-W5
    MK-->>C: Return bobot kriteria

    C->>C: normalisasiMatriks()
    C->>C: hitungNilaiPreferensi(Vi)
    C->>C: urutkanRanking()

    C->>MH: simpanHasil(data_ranking)
    MH->>DB: INSERT INTO tb_hasil
    DB-->>MH: Berhasil disimpan

    C-->>V: Kirim hasil (matriks, normalisasi, Vi, ranking)
    V-->>Admin: Menampilkan tabel ranking Top 5
```

---

## 4. Class Diagram

```mermaid
classDiagram
    class UserModel {
        -id : int
        -username : string
        -password : string
        -nama : string
        +login(username, password) : bool
        +getUserById(id) : array
    }

    class KriteriaModel {
        -id : int
        -kode : string
        -nama_kriteria : string
        -jenis : string
        -bobot : float
        +getAll() : array
        +getById(id) : array
        +create(data) : bool
        +update(id, data) : bool
        +delete(id) : bool
        +updateBobot(id, bobot) : bool
    }

    class AlternatifModel {
        -id : int
        -nama_siswa : string
        -kelas : string
        -bidang_osn : string
        +getAll() : array
        +getByBidang(bidang) : array
        +getById(id) : array
        +create(data) : bool
        +update(id, data) : bool
        +delete(id) : bool
        +countByBidang(bidang) : int
    }

    class PenilaianModel {
        -id : int
        -id_alternatif : int
        -id_kriteria : int
        -nilai : float
        +getByAlternatif(id_alt) : array
        +getByBidang(bidang) : array
        +create(data) : bool
        +update(id, data) : bool
        +getMatriksKeputusan(bidang) : array
    }

    class AhpModel {
        -id : int
        -id_kriteria_1 : int
        -id_kriteria_2 : int
        -nilai : float
        +getMatriksPerbandingan() : array
        +simpanPerbandingan(data) : bool
        +hitungNormalisasi() : array
        +hitungEigenvector() : array
        +hitungLambdaMax() : float
        +hitungCI() : float
        +hitungCR() : float
    }

    class HasilModel {
        -id : int
        -id_alternatif : int
        -bidang_osn : string
        -nilai_preferensi : float
        -ranking : int
        +simpanHasil(data) : bool
        +getHasilByBidang(bidang) : array
        +getTop5(bidang) : array
    }

    PenilaianModel "1" --> "*" AlternatifModel : id_alternatif
    PenilaianModel "1" --> "*" KriteriaModel : id_kriteria
    AhpModel "1" --> "*" KriteriaModel : id_kriteria_1, id_kriteria_2
    HasilModel "1" --> "*" AlternatifModel : id_alternatif
```

---

## 5. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    TB_USERS {
        int id PK
        varchar username UK
        varchar password
        varchar nama
        timestamp created_at
    }

    TB_KRITERIA {
        int id PK
        varchar kode UK
        varchar nama_kriteria
        enum jenis
        decimal bobot
    }

    TB_ALTERNATIF {
        int id PK
        varchar nama_siswa
        varchar kelas
        enum bidang_osn
    }

    TB_PENILAIAN {
        int id PK
        int id_alternatif FK
        int id_kriteria FK
        decimal nilai
    }

    TB_PERBANDINGAN {
        int id PK
        int id_kriteria_1 FK
        int id_kriteria_2 FK
        decimal nilai
    }

    TB_HASIL {
        int id PK
        int id_alternatif FK
        enum bidang_osn
        decimal nilai_preferensi
        int ranking
        timestamp created_at
    }

    TB_KRITERIA ||--o{ TB_PENILAIAN : "dinilai pada"
    TB_ALTERNATIF ||--o{ TB_PENILAIAN : "memiliki nilai"
    TB_KRITERIA ||--o{ TB_PERBANDINGAN : "dibandingkan (kriteria 1)"
    TB_KRITERIA ||--o{ TB_PERBANDINGAN : "dibandingkan (kriteria 2)"
    TB_ALTERNATIF ||--o{ TB_HASIL : "mendapat ranking"
```

---

## 6. Flowchart Algoritma AHP-SAW

### 6.1 Flowchart AHP (Pembobotan Kriteria)

```mermaid
flowchart TD
    A([Start]) --> B[/Input: Matriks Perbandingan Berpasangan 5x5/]
    B --> C["Hitung jumlah setiap kolom<br>kolom_j = Σ a_ij"]
    C --> D["Normalisasi matriks<br>r_ij = a_ij / kolom_j"]
    D --> E["Hitung bobot prioritas<br>W_i = rata-rata baris ke-i"]
    E --> F["Hitung λ max<br>λmax = rata-rata (AW_i / W_i)"]
    F --> G["Hitung CI<br>CI = (λmax - n) / (n - 1)"]
    G --> H["Hitung CR<br>CR = CI / RI (RI=1.12 untuk n=5)"]
    H --> I{CR ≤ 0.1?}
    I -->|Ya| J[/Output: Bobot W1, W2, W3, W4, W5/]
    J --> K[Simpan bobot ke database]
    K --> L([End])
    I -->|Tidak| M[/Output: Peringatan tidak konsisten/]
    M --> B
```

### 6.2 Flowchart SAW (Perangkingan per Bidang)

```mermaid
flowchart TD
    A([Start]) --> B[/Input: Bidang OSN yang dipilih/]
    B --> C[Ambil data siswa pada bidang tersebut]
    C --> D[Ambil nilai penilaian C1-C5 setiap siswa]
    D --> E[Ambil bobot W1-W5 dari hasil AHP]
    E --> F[Susun Matriks Keputusan X]
    F --> G["Cari nilai max setiap kriteria j<br>max_j = max(x_ij)"]
    G --> H["Normalisasi matriks<br>r_ij = x_ij / max_j (Benefit)"]
    H --> I["Hitung Nilai Preferensi<br>Vi = Σ (Wj × rij) untuk setiap siswa i"]
    I --> J[Urutkan Vi dari terbesar ke terkecil]
    J --> K[Tentukan ranking 1 sampai n]
    K --> L[Simpan hasil ke database]
    L --> M[/Output: Tabel ranking Top 5 siswa/]
    M --> N([End])
```

---

## 📋 Checklist Diagram untuk Bab 4

| No | Diagram | Sub-bab | Status |
|---|---|---|---|
| 1 | Use Case Diagram | 4.2.3a | ☐ |
| 2 | Activity Diagram — Login | 4.2.3b | ☐ |
| 3 | Activity Diagram — Kelola Siswa | 4.2.3b | ☐ |
| 4 | Activity Diagram — Input Penilaian | 4.2.3b | ☐ |
| 5 | Activity Diagram — Proses AHP-SAW | 4.2.3b | ☐ |
| 6 | Activity Diagram — Cetak Laporan | 4.2.3b | ☐ |
| 7 | Sequence Diagram — Login | 4.2.3c | ☐ |
| 8 | Sequence Diagram — Proses AHP | 4.2.3c | ☐ |
| 9 | Sequence Diagram — Proses SAW | 4.2.3c | ☐ |
| 10 | Class Diagram | 4.2.3d | ☐ |
| 11 | ERD | 4.2.4 | ☐ |
| 12 | Flowchart AHP | 4.2.6 | ☐ |
| 13 | Flowchart SAW | 4.2.6 | ☐ |

> [!NOTE]
> **Total: 13 diagram** yang perlu dirender menjadi gambar dan dimasukkan ke dokumen skripsi.
>
> **Cara tercepat:**
> 1. Buka [mermaid.live](https://mermaid.live)
> 2. Paste kode Mermaid dari dokumen ini satu per satu
> 3. Export sebagai PNG
> 4. Masukkan ke Word dengan caption: *"Gambar 4.x [Nama Diagram]"*
