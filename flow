# Product Requirements Document (PRD)
**Sistem Informasi Sekolah & E-Learning**

## 1. Pendahuluan
**1.1 Tujuan Dokumen**
Dokumen ini mendefinisikan persyaratan fungsional, non-fungsional, dan teknis untuk aplikasi "Sistem Informasi Sekolah dan E-Learning". Dokumen ini menjadi acuan utama dalam pengembangan, pengujian, dan operasional sistem.

**1.2 Visi Produk**
Membangun sebuah platform terpusat yang memudahkan sekolah dalam mengelola data akademik, administrasi tenaga pengajar, memfasilitasi pembelajaran jarak jauh (E-learning), serta menunjang proses transparansi keuangan melalui pembayaran tagihan secara digital dan otomatis.

---

## 2. Arsitektur Pengguna (Use Case & Flow)

Terdapat 3 aktor utama di dalam sistem ini: Admin, Guru, dan Siswa. Berikut merupakan gambaran fungsionalitas besar (Use Case) masing-masing aktor:

```mermaid
flowchart LR
    Admin([Admin / TU])
    Guru([Teacher / Guru])
    Siswa([Student / Siswa])

    subgraph Data Master
        Master[Kelola Master Data Kelas, Mapel, Tahun Ajaran]
    end

    subgraph Modul E-Learning & Akademik
        Upload[Upload Materi, Tugas, & Jadwal]
        Nilai[Beri Nilai & Absen]
        Akses[Akses Materi & Kumpul Tugas]
        LaporanBelajar[Lihat Transkrip & Jadwal]
    end

    subgraph Modul Keuangan
        KelolaTagihan[Kelola Kategori & Generate Tagihan]
        LaporanUang[Export Laporan Keuangan]
        Bayar[Bayar Tagihan via Gateway]
    end

    Guru --> Upload
    Guru --> Nilai
    
    Siswa --> Akses
    Siswa --> LaporanBelajar
    Siswa --> Bayar
    
    Admin --> Master
    Admin --> KelolaTagihan
    Admin --> LaporanUang
```

---

## 3. Fitur Utama (Functional Requirements)

### 3.1 Modul Data Master & Pengaturan (Admin)
- **Manajemen Siswa & Alumni:** CRUD data siswa, import/export data (Excel), serta pengelolaan status alumni.
- **Manajemen Guru:** Pendaftaran dan pengelolaan data guru.
- **Manajemen Kelas & Jurusan:** Pengaturan pembagian kelas, program studi/jurusan, dan fitur penentuan kenaikan kelas (Promotion).
- **Manajemen Tahun Ajaran:** Pengaturan tahun ajaran aktif untuk siklus akademik.
- **Pengaturan Sistem:** Mengatur preferensi umum aplikasi (Settings).
- **Pengumuman:** Pembuatan informasi / pengumuman (Announcements) yang terpusat.

### 3.2 Modul Akademik & E-Learning
- **Manajemen Jadwal Pelajaran (Schedules):** Membuat jadwal, mengunduh *template*, serta melakukan *import/export*.
- **Materi & Tugas Belajar (Materials & Assignments):**

**Alur (Workflow) Penugasan E-Learning:**
```mermaid
stateDiagram-v2
    [*] --> Guru_Buat_Tugas
    Guru_Buat_Tugas --> Tugas_Tersedia: Muncul di Dashboard Siswa
    Tugas_Tersedia --> Siswa_Mengerjakan
    Siswa_Mengerjakan --> Siswa_Mengumpulkan
    Siswa_Mengumpulkan --> Menunggu_Diperiksa
    Menunggu_Diperiksa --> Guru_Memberi_Nilai
    Guru_Memberi_Nilai --> Nilai_Direkapitulasi
    Nilai_Direkapitulasi --> [*]
```

### 3.3 Modul Administrasi Guru
- **Pemenuhan Administrasi (Teacher):** Guru dapat mengunggah berkas administrasi kelengkapan mengajar.
- **Monitoring (Admin):** Admin / Kepala Sekolah memantau tingkat kelengkapan administrasi masing-masing guru.

### 3.4 Modul Keuangan & Pembayaran
- **Kategori & Item Pembayaran:** Penentuan jenis-jenis biaya sekolah (SPP, PTS, PAS, dll).
- **Manajemen Tagihan (Bills):** Admin membuat tagihan siswa (mendukung *auto-sync*).
- **Pembayaran Online (Siswa):** Integrasi Payment Gateway Midtrans.

**Sequence Diagram Alur Pembayaran Midtrans:**
```mermaid
sequenceDiagram
    participant Siswa
    participant Sistem as Sistem E-Learning
    participant Midtrans

    Siswa->>Sistem: Memilih tagihan dan klik "Bayar"
    Sistem->>Sistem: Validasi Tagihan & Buat Transaksi (Pending)
    Sistem->>Midtrans: Request API - Create Snap Transaction
    Midtrans-->>Sistem: Mengembalikan Snap Token & URL
    Sistem-->>Siswa: Membuka Snap Popup / Halaman Pembayaran
    Siswa->>Midtrans: Melakukan pembayaran (Bank Transfer, QRIS, e-Wallet)
    Midtrans-->>Siswa: Info pembayaran sukses (di layar Midtrans)
    
    note over Sistem, Midtrans: Webhook Asynchronous
    Midtrans->>Sistem: HTTP POST /api/midtrans/webhook
    Sistem->>Sistem: Verifikasi Signature Key Midtrans
    Sistem->>Sistem: Update Transaksi -> Success
    Sistem->>Sistem: Update Status Tagihan -> Paid
```

---

## 4. Entity Relationship Diagram (ERD) Inti
Skema hubungan relasional dari modul-modul sistem dapat digambarkan secara konseptual melalui ERD berikut:

```mermaid
erDiagram
    USERS ||--o{ STUDENTS : "is a"
    USERS ||--o{ TEACHERS : "is a"
    
    USERS {
        bigInt id PK
        string name
        string email
        string role
    }
    
    CLASSROOMS ||--o{ STUDENTS : "contains"
    CLASSROOMS {
        bigInt id PK
        string name
    }
    
    SUBJECTS ||--o{ MATERIALS : "has"
    SUBJECTS ||--o{ ASSIGNMENTS : "has"
    SUBJECTS {
        bigInt id PK
        string name
    }
    
    TEACHERS ||--o{ ASSIGNMENTS : "creates"
    STUDENTS ||--o{ SUBMISSIONS : "submits"
    ASSIGNMENTS ||--o{ SUBMISSIONS : "receives"
    
    STUDENTS ||--o{ BILLS : "owes"
    BILLS ||--o{ TRANSACTIONS : "paid_via"
    
    BILLS {
        bigInt id PK
        bigInt student_id FK
        decimal amount
        string status
    }
    
    TRANSACTIONS {
        bigInt id PK
        string order_id
        decimal gross_amount
        string status
    }
```

---

## 5. Kebutuhan Teknis (Technical Requirements)
- **Backend Framework:** Laravel 11 (berbasis PHP ^8.2).
- **Database:** MySQL / MariaDB (kompatibel juga dengan SQLite untuk *testing*).
- **Sistem Pembayaran:** Midtrans PHP SDK (`midtrans/midtrans-php`).
- **File & Export/Import:** 
  - `maatwebsite/excel` (Versi 3.1+) untuk *import/export* data (*spreadsheet*).
  - `barryvdh/laravel-dompdf` (Versi ^3.1) untuk laporan PDF.
- **Frontend Layer:** Blade Template Engine (menggunakan arsitektur monolitik Laravel), HTML/CSS/JS konvensional.
- **Routing & Autentikasi:** *Middleware* Auth bawaan Laravel dikombinasikan dengan *Custom Role Middleware* (`role:admin`, `role:teacher`, `role:student`).

---

## 6. Non-Functional Requirements
- **Keamanan (Security):** 
  - Proteksi form dari *CSRF*.
  - Endpoint Webhook payment dilindungi logika validasi Signature Midtrans.
- **Performa:** Indexing pada Foreign Key untuk laporan keuangan skala besar.
- **Aksesibilitas:** Antarmuka (UI/UX) didesain responsif untuk memudahkan akses Guru dan Siswa dari *Smartphone*.
