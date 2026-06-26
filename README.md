# EduPay SMK - Sistem Pembayaran & E-Learning Digital

EduPay SMK adalah platform terintegrasi yang dirancang khusus untuk Sekolah Menengah Kejuruan (SMK) guna mendigitalisasi proses pembayaran tagihan sekolah dan manajemen pembelajaran (E-Learning).

## Fitur Utama

### 1. Manajemen Keuangan & Pembayaran
- **Pembayaran Online**: Integrasi dengan **Midtrans Payment Gateway** untuk pembayaran otomatis via GoPay, QRIS, Transfer Bank (Virtual Account), dan Minimarket.
- **Dukungan Cicilan**: Siswa dapat mencicil tagihan pembayaran.
- **Kategori Pembayaran**: Pengelolaan tagihan berdasarkan kategori (PTS, PAS, SPP, Ujikom, Kunjungan Industri) dan Semester.
- **Laporan Keuangan**: Cetak laporan transaksi dan bukti kwitansi otomatis dalam format PDF.

### 2. E-Learning & Akademik
- **Materi & Tugas**: Guru dapat mengunggah materi pelajaran dan memberikan tugas kepada siswa.
- **Pengumpulan Tugas**: Siswa dapat mengunggah jawaban tugas langsung melalui portal.
- **Absensi Terintegrasi**: Sistem presensi harian siswa yang dikelola oleh guru.
- **Rekap Nilai (Transkrip)**: Perhitungan nilai akhir otomatis berdasarkan persentase bobot Absensi, Tugas, PTS, dan PAS.

### 3. Administrasi Guru (Kurikulum Merdeka)
- **Unggah Dokumen Mengajar**: Guru wajib mengunggah 7 dokumen kelengkapan mengajar (CP, TP, ATP, Modul Ajar, Prota, Promes, Buku Nilai).
- **Pantau Administrasi**: Kepala sekolah atau Admin dapat memonitor persentase kelengkapan administrasi masing-masing guru.

### 4. Pengaturan Aplikasi Dinamis
- Ubah Nama Sekolah, Email, Telepon, dan Alamat langsung dari antarmuka Admin.
- Ubah **Logo Aplikasi** dan **Warna Tema** (Color Picker) secara dinamis tanpa menyentuh baris kode.
- Data Kepala Sekolah untuk tanda tangan otomatis di dokumen PDF.

---

## Prasyarat (Requirements)
- PHP 8.2 atau lebih baru
- Composer
- MySQL atau MariaDB
- Node.js & NPM (untuk kompilasi aset jika diperlukan)

## Instalasi

1. Clone repositori ini:
   ```bash
   git clone https://github.com/IbnuSyina77777/elearningda.git
   cd elearningda
   ```

2. Install dependensi PHP:
   ```bash
   composer install
   ```

3. Salin file `.env.example` ke `.env` dan sesuaikan konfigurasi database:
   ```bash
   cp .env.example .env
   ```

4. Generate Application Key:
   ```bash
   php artisan key:generate
   ```

5. Jalankan migrasi dan seeder database:
   ```bash
   php artisan migrate --seed
   ```
   *(Catatan: Seeder akan membuatkan akun default untuk Admin, Guru, dan Siswa)*

6. Hubungkan folder storage:
   ```bash
   php artisan storage:link
   ```

7. Jalankan server lokal:
   ```bash
   php artisan serve
   ```

## Integrasi Midtrans
Aplikasi ini menggunakan Midtrans untuk gerbang pembayaran. Pastikan Anda mengisi API Key di file `.env` Anda:
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```
Jangan lupa atur **Notification URL** di dashboard Midtrans menunjuk ke `https://domain-anda.com/api/midtrans/webhook`.

---
&copy; 2026 E-Learning SMK. All rights reserved.
