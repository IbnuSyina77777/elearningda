$menuCode = @"
@startwbs
skinparam BackgroundColor white
skinparam RoundCorner 5
skinparam DefaultFontName Arial
skinparam node {
  BackgroundColor #e8f3fa
  BorderColor #1c4a6b
  FontColor black
}

* Aplikasi E-Learning & Pembayaran
** Login / Autentikasi
** Menu Admin (Administrator)
*** Dashboard
*** Data Master
**** Kelola Siswa
**** Kelola Guru
**** Kelas & Jurusan
**** Mata Pelajaran
**** Tahun Ajaran
*** Manajemen Akademik
**** Jadwal Pelajaran
*** Manajemen Keuangan
**** Item Pembayaran
**** Kelola Tagihan (Bills)
**** Riwayat Transaksi
*** Laporan
*** Pengaturan (Setting)
** Menu Guru (Teacher)
*** Dashboard
*** Akademik
**** Jadwal Mengajar
**** Kelola Materi
**** Kelola Tugas & Ujian
**** Input Presensi
*** Penilaian
**** Cek Tugas Masuk
**** Input Nilai Akhir
** Menu Siswa (Student)
*** Dashboard
*** Ruang Belajar
**** Akses Materi & Modul
**** Kerjakan Tugas / Ujian
**** Isi Presensi
*** Informasi Akademik
**** Jadwal Pelajaran
**** Lihat Transkrip Nilai
*** Administrasi Keuangan
**** Cek Tagihan Aktif
**** Bayar via Midtrans
**** Riwayat & Invoice
@endwbs
"@

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

Write-Host "Generating Struktur Menu PNG..."
try {
    Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $menuCode -ContentType "text/plain" -OutFile "c:\web\elearning\diagram\struktur_menu.png" -ErrorAction Stop
    Write-Host "Success!"
} catch {
    Write-Host "Error generating diagram: $_"
}
