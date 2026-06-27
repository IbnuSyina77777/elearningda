# Product Requirements Document (PRD)
**Sistem Informasi Sekolah & E-Learning**

## 1. Pendahuluan
**1.1 Tujuan Dokumen**
Dokumen ini mendefinisikan persyaratan fungsional, non-fungsional, dan teknis untuk aplikasi "Sistem Informasi Sekolah dan E-Learning". Dokumen ini menjadi acuan utama dalam pengembangan, pengujian, dan operasional sistem.

**1.2 Visi Produk**
Membangun sebuah platform terpusat yang memudahkan sekolah dalam mengelola data akademik, administrasi tenaga pengajar, memfasilitasi pembelajaran jarak jauh (E-learning), serta menunjang proses transparansi keuangan melalui pembayaran tagihan secara digital dan otomatis.

---

## 2. Pengguna (Target Audience)
Sistem ini dirancang dengan sistem autentikasi dan otorisasi untuk 3 (tiga) peran utama:

1. **Admin / Tata Usaha**
   Bertanggung jawab penuh atas pengelolaan data master (siswa, guru, kelas), manajemen keuangan, pengaturan sistem, dan pemantauan aktivitas sekolah.
2. **Teacher (Guru)**
   Bertanggung jawab atas manajemen kegiatan belajar mengajar (KBM), seperti mengunggah materi, memberikan tugas, menilai ujian, mengisi absensi, serta melengkapi administrasi guru.
3. **Student (Siswa)**
   Pengguna akhir yang memanfaatkan sistem untuk mengakses materi pembelajaran, mengumpulkan tugas, melihat jadwal pelajaran, melihat transkrip nilai, serta melakukan pembayaran tagihan sekolah secara online.

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
- **Manajemen Jadwal Pelajaran (Schedules):**
  - **Admin:** Membuat jadwal, mengunduh *template*, serta melakukan *import* dan *export*.
  - **Guru & Siswa:** Melihat jadwal pelajaran sesuai kelas masing-masing.
- **Mata Pelajaran (Subjects):** Pengaturan silabus atau daftar mata pelajaran.
- **Materi & Tugas Belajar (Materials & Assignments):**
  - **Guru:** Mengunggah bahan ajar, membuat tugas, dan menentukan tenggat waktu.
  - **Siswa:** Mengakses / mengunduh materi, serta mengunggah jawaban tugas (Submissions).
  - **Guru:** Memeriksa dan memberikan nilai (Grading) atas tugas yang telah dikumpulkan.
- **Ujian & Penilaian (Exams & Grades):**
  - **Guru:** Memasukkan nilai hasil ujian/tugas (Exam Scores).
  - **Admin & Guru:** Melihat dan mengekspor rekapitulasi nilai.
  - **Siswa:** Melihat rincian nilai dan rapor / transkrip hasil belajar.
- **Kehadiran (Attendances):** 
  - Guru dapat mengisi absensi siswa di kelas.
  - Sistem mengakumulasikan bobot kehadiran (Attendance Weights).

### 3.3 Modul Administrasi Guru
- **Pemenuhan Administrasi (Teacher):** Guru dapat mengunggah berkas administrasi kelengkapan mengajar.
- **Monitoring (Admin):** Admin / Kepala Sekolah dapat memantau tingkat kelengkapan administrasi masing-masing guru berdasarkan mata pelajaran.

### 3.4 Modul Keuangan & Pembayaran
- **Kategori & Item Pembayaran (Payment Category & Item):** Penentuan jenis-jenis biaya sekolah (SPP, Uang Gedung, Ekstrakurikuler, dll).
- **Manajemen Tagihan (Bills):**
  - Admin membuat tagihan siswa.
  - Tersedia fitur sinkronisasi otomatis (*auto-sync*) dan penghapusan massal (*destroy all*).
- **Pembayaran Online (Siswa):**
  - Siswa dapat melihat rincian tagihan (Bills).
  - Terintegrasi dengan Midtrans (Payment Gateway) sehingga siswa dapat membayar menggunakan VA, e-Wallet, atau metode lainnya.
- **Transaksi & Kuitansi (Transactions):**
  - Pencatatan transaksi secara *real-time* (didukung Webhook Midtrans).
  - Admin dan Siswa dapat melihat atau mengunduh cetak bukti transaksi (Receipt).
- **Laporan Keuangan (Reports):** Admin dapat membuat rekap pendapatan keuangan dan mengekspor laporan ke dalam format PDF.

---

## 4. Kebutuhan Teknis (Technical Requirements)
Sistem dibangun menggunakan *stack* teknologi modern untuk memastikan efisiensi dan kemudahan *maintenance*.

- **Backend Framework:** Laravel 11 (berbasis PHP ^8.2).
- **Database:** MySQL / MariaDB (kompatibel juga dengan SQLite untuk *testing*).
- **Sistem Pembayaran:** Midtrans PHP SDK (`midtrans/midtrans-php`).
- **File & Export/Import:** 
  - `maatwebsite/excel` (Versi 3.1+) untuk *import/export* data (*spreadsheet*).
  - `barryvdh/laravel-dompdf` (Versi ^3.1) untuk *generate* laporan dan kuitansi dalam bentuk PDF.
- **Frontend Layer:** Blade Template Engine (menggunakan arsitektur monolitik Laravel), dikombinasikan dengan HTML/CSS/JS konvensional.
- **Routing & Autentikasi:** Menggunakan *middleware* Auth bawaan Laravel dikombinasikan dengan *Custom Role Middleware* (`role:admin`, `role:teacher`, `role:student`).

---

## 5. Non-Functional Requirements
- **Keamanan (Security):** 
  - Setiap form diamankan dari ancaman *CSRF*.
  - *Endpoint* pembayaran (Webhook) dibuat *stateless* dan diproteksi berdasarkan Signature Midtrans.
  - Pembatasan hak akses *strict* berbasis URL dan data antar role.
- **Reliabilitas (Reliability):** Database dan migrasi sudah terstruktur (mendukung integritas *foreign key* dan *cascading*).
- **Aksesibilitas (Usability):** Antarmuka (UI/UX) didesain agar responsif untuk kemudahan akses baik menggunakan Desktop maupun *Mobile* (Ponsel/Tablet).

---

## 6. Rencana Pengembangan Lanjutan (Future Enhancements)
*(Rekomendasi Opsional)*
- Integrasi ke notifikasi pihak ketiga (seperti WhatsApp Bot/Email) agar siswa mendapat *reminder* tentang jatuh tempo tagihan pembayaran atau tugas baru.
- Penambahan modul perpustakaan digital (E-Library) untuk melengkapi kebutuhan literasi E-Learning.
- Ujian Online (CBT - Computer Based Test) terintegrasi yang memungkinkan siswa langsung ujian pilihan ganda/essay di dalam sistem.
