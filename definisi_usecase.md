# Definisi Aktor dan Use Case
**Sistem E-Learning & Pembayaran**

Berikut adalah penjelasan dan definisi dari masing-masing aktor serta Use Case yang terdapat di dalam diagram:

## 1. Definisi Aktor (Actors)
Aktor adalah entitas (bisa berupa manusia atau sistem lain) yang berinteraksi langsung dengan sistem. Pada aplikasi ini terdapat 3 aktor utama:

- **Admin**: Pihak pengelola atau administrator sistem (seperti staf Tata Usaha). Admin memiliki hak akses khusus untuk melakukan manajemen data operasional secara keseluruhan, memantau akun pengguna, dan melihat rekapan laporan.
- **Guru**: Tenaga pendidik yang bertanggung jawab pada proses akademik. Guru berinteraksi dengan sistem untuk menyebarkan bahan ajar ke kelas dan melakukan evaluasi/penilaian terhadap siswa.
- **Siswa**: Pengguna utama (peserta didik) dari sistem e-learning. Siswa bertindak sebagai penerima materi, peserta ujian, serta pihak yang berkewajiban melakukan pelunasan tagihan administrasi sekolah.

---

## 2. Definisi Use Case (Fungsionalitas)
Use Case merupakan fungsionalitas, layanan, atau tindakan yang disediakan oleh sistem bagi para aktor.

### Use Case Bersama
- **Login ke Sistem**: Proses autentikasi wajib bagi semua aktor (Admin, Guru, Siswa). Aktor harus memasukkan kredensial (seperti *username* dan *password*) yang valid untuk mendapatkan sesi akses ke dalam sistem.

### Use Case Admin
- **Kelola Data Pengguna**: Layanan bagi Admin untuk memanajemen data master pengguna. Ini mencakup proses pendaftaran (tambah akun baru), pembaruan data, hingga penghapusan profil Siswa maupun Guru.
- **Lihat Laporan**: Layanan bagi Admin untuk menarik, memantau, dan mencetak laporan sistem. Laporan ini bisa berupa laporan pembayaran tagihan maupun statistik aktivitas akademik.

### Use Case Guru
- **Buat Materi Pelajaran**: Layanan bagi Guru untuk menyusun, mengunggah lampiran (dokumen/video), dan mendistribusikan bahan ajar ke suatu mata pelajaran agar dapat dipelajari oleh siswa.
- **Kelola Tugas & Nilai**: Layanan bagi Guru untuk mengatur penugasan/ujian, memeriksa hasil pengerjaan siswa, memberikan nilai evaluasi, serta menyimpannya ke dalam arsip nilai (transkrip).

### Use Case Siswa
- **Ikuti Pelajaran**: Layanan bagi Siswa untuk memasuki kelas yang telah didaftarkan, melihat aktivitas kelas, serta mengunduh/mempelajari materi yang diunggah oleh guru.
- **Kerjakan Ujian**: Layanan bagi Siswa untuk mengakses halaman evaluasi, lalu menjawab soal-soal kuis atau ujian dalam batas waktu yang ditentukan.
- **Kumpulkan Jawaban** *(«include» dari Kerjakan Ujian)*: Sub-proses wajib yang dieksekusi bersamaan saat ujian berakhir. Berfungsi merekam dan mengunci jawaban siswa ke dalam *database* sistem.
- **Proses Pembayaran**: Layanan bagi Siswa untuk mengecek daftar tagihan yang belum dibayar (misal: uang pangkal, SPP bulanan), lalu memilih metode pembayaran dan memproses transaksi pelunasan.
- **Lihat Invoice** *(«include» dari Proses Pembayaran)*: Sub-proses yang melekat pada proses pembayaran. Menyediakan fasilitas bagi siswa untuk mencetak atau melihat bukti rincian tagihan (kwitansi elektronik) dari pembayaran tersebut.
