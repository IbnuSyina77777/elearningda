# Walkthrough: Modul E-Learning 🎉

Aplikasi kini telah berubah dari sekadar aplikasi sistem pembayaran (SPP) menjadi sebuah **Platform Edukasi Terpadu (E-Learning)** yang mendukung peran Admin, Guru, dan Siswa secara penuh!

## Apa Saja yang Telah Dibuat?

Berikut adalah alur dan fitur baru yang kini sudah bisa digunakan di dalam aplikasi:

### 1. Peran Admin (Tata Usaha / Pengelola Data)
- **Kelola Data Guru**: Admin dapat menambah, mengedit, dan menghapus akun guru. Setiap guru akan otomatis mendapatkan hak akses (role: `teacher`).
- **Data Mata Pelajaran**: Admin mendaftarkan mata pelajaran dan menghubungkannya dengan **Guru Pengajar** dan **Kelas Tujuan**.
  - Contoh: Menugaskan Pak Budi (Guru) untuk mengajar Bahasa Indonesia (Mapel) di kelas X RPL 1 (Kelas).

### 2. Portal Khusus Guru (Teacher Portal)
Saat guru berhasil login, mereka akan diarahkan ke dashboard khusus yang sangat berbeda dengan Admin maupun Siswa.
- **Dashboard Ringkasan**: Guru bisa melihat total mapel, total materi, total tugas, dan berapa tugas siswa yang belum dinilai.
- **Menu Dinamis**: Sidebar di kiri akan otomatis menyesuaikan daftar kelas/mapel yang diajarkan oleh guru tersebut.
- **Manajemen Materi (Upload & Teks)**: Guru bisa membagikan materi berbentuk teks langsung, maupun melampirkan file PDF, PPT, Word, Gambar, atau ZIP.
- **Pembuatan Tugas & Deadline**: Guru bisa membuat tugas terstruktur, memberikan instruksi detail, melampirkan file soal, dan menyetel batas akhir pengumpulan (deadline otomatis menggunakan datetime picker).
- **Penilaian (Grading System)**: 
  - Guru dapat melihat daftar seluruh siswa di kelas tersebut, mengecek siapa saja yang sudah dan belum mengumpulkan.
  - Guru bisa mendownload file jawaban siswa, memberikan nilai (0-100), dan menuliskan feedback personal.
  - Terdapat indikator "Terlambat" merah jika siswa mengumpulkan setelah deadline.

### 3. Portal E-Learning Siswa
Siswa kini memiliki pengalaman belajar yang kaya di dalam satu aplikasi (bersama dengan fitur pembayaran).
- **Mata Pelajaran Saya**: Siswa hanya akan melihat mapel yang ditugaskan khusus untuk kelas mereka saja.
- **Detail Mapel**: Ketika masuk ke suatu mapel, siswa akan disajikan dua kolom utama: daftar urut materi untuk dipelajari, dan daftar tugas yang harus dikerjakan.
- **Pengumpulan Tugas**:
  - Siswa dapat melihat instruksi mendetail dari guru dan batas akhir waktu pengumpulan.
  - Jika deadline belum berakhir, siswa dapat mengupload file jawaban beserta catatan opsional.
  - Jika deadline telah berakhir, form pengumpulan akan terkunci dan muncul peringatan merah.
  - Setelah dikumpulkan, siswa bisa melihat **Nilai** dan **Feedback** yang diberikan guru setelah tugas tersebut diperiksa.

---

## Cara Melakukan Pengecekan (Testing)

Anda bisa menguji flow lengkap ini dengan langkah-langkah berikut:

1. **Persiapan Data (Login Admin)**:
   - Buat 1 Guru baru di menu **Data Guru**.
   - Buat 1 Mata Pelajaran baru di menu **Mata Pelajaran**. Assign ke Guru tersebut dan Kelas tertentu (pastikan ada siswa di kelas ini).
   - Logout.
2. **Uji Portal Guru (Login Guru)**:
   - Login dengan akun guru yang baru dibuat.
   - Pilih mata pelajaran di sidebar.
   - Coba **Tambah Materi** (upload PDF atau file bebas).
   - Coba **Buat Tugas** (atur deadline untuk besok).
   - Logout.
3. **Uji Portal Siswa (Login Siswa)**:
   - Login dengan siswa yang berada di kelas mapel tersebut.
   - Buka menu **Mata Pelajaran** di sidebar.
   - Masuk ke mapel, coba download materi.
   - Klik **Detail Tugas**, lalu upload file untuk mengumpulkan.
   - Logout.
4. **Uji Penilaian (Kembali Login Guru)**:
   - Login lagi sebagai Guru.
   - Masuk ke tugas tersebut -> Pengumpulan.
   - Berikan nilai dan feedback untuk siswa tadi.

Semua fitur E-Learning ini sudah berjalan dengan lancar, elegan, dan siap Anda gunakan!
