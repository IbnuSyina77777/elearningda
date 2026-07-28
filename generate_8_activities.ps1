$commonStyle = @"
skinparam BackgroundColor white
skinparam ActivityBackgroundColor #e1f5fe
skinparam ActivityBorderColor #0288d1
skinparam ActivityDiamondBackgroundColor #fff2cc
skinparam ActivityDiamondBorderColor #d6b656
skinparam SwimlaneBorderColor #333333
skinparam ArrowColor #333333
skinparam DefaultFontName Arial
"@

$diagrams = @(
    @{
        name = "1_Login_ke_Sistem"
        code = @"
@startuml
$commonStyle
|Pengguna (Admin/Guru/Siswa)|
start
:Buka Halaman Login;
:Masukkan Username & Password;
|Sistem|
:Validasi Kredensial;
if (Kredensial Valid?) then (Ya)
  :Tentukan Role Pengguna;
  :Arahkan ke Dashboard;
  stop
else (Tidak)
  :Tampilkan Pesan Error;
  stop
endif
@enduml
"@
    },
    @{
        name = "2_Kelola_Data_Pengguna"
        code = @"
@startuml
$commonStyle
|Admin|
start
:Pilih Menu Data Pengguna;
:Pilih Tambah/Edit Data;
:Isi Form Data (Siswa/Guru);
:Klik Simpan;
|Sistem|
:Validasi Input Data;
if (Data Valid?) then (Ya)
  :Simpan Data ke Database;
  :Tampilkan Notifikasi Sukses;
else (Tidak)
  :Tampilkan Peringatan Error;
endif
|Admin|
stop
@enduml
"@
    },
    @{
        name = "3_Lihat_Laporan"
        code = @"
@startuml
$commonStyle
|Admin|
start
:Buka Menu Laporan;
:Pilih Jenis Laporan (Keuangan/Akademik);
:Filter Rentang Waktu;
|Sistem|
:Ambil Data dari Database;
:Kalkulasi Ringkasan Data;
:Tampilkan Data Laporan;
|Admin|
:Eksport Laporan (PDF/Excel);
|Sistem|
:Generate File Laporan;
:Download File;
|Admin|
stop
@enduml
"@
    },
    @{
        name = "4_Buat_Materi_Pelajaran"
        code = @"
@startuml
$commonStyle
|Guru|
start
:Buka Mata Pelajaran;
:Pilih Menu Tambah Materi;
:Input Judul, Deskripsi & File;
:Klik Publish;
|Sistem|
:Simpan Materi ke Database;
:Kirim Notifikasi ke Siswa;
:Tampilkan Materi di Kelas;
|Guru|
stop
@enduml
"@
    },
    @{
        name = "5_Kelola_Tugas_Nilai"
        code = @"
@startuml
$commonStyle
|Guru|
start
:Buka Menu Tugas / Nilai;
:Pilih Tugas Siswa;
|Sistem|
:Tampilkan Daftar Jawaban Siswa;
|Guru|
:Review Jawaban Siswa;
:Input Nilai & Feedback;
:Klik Simpan Nilai;
|Sistem|
:Update Nilai ke Database;
:Update Status Tugas Siswa;
|Guru|
stop
@enduml
"@
    },
    @{
        name = "6_Ikuti_Pelajaran"
        code = @"
@startuml
$commonStyle
|Siswa|
start
:Buka Halaman Dashboard;
:Pilih Mata Pelajaran;
|Sistem|
:Tampilkan Daftar Materi Tersedia;
|Siswa|
:Klik Judul Materi;
|Sistem|
:Catat Log Akses Materi;
:Tampilkan Konten Materi / Video;
|Siswa|
:Membaca / Menonton Materi;
:Download Lampiran (Jika Ada);
stop
@enduml
"@
    },
    @{
        name = "7_Proses_Pembayaran"
        code = @"
@startuml
$commonStyle
|Siswa|
start
:Buka Menu Tagihan Pembayaran;
|Sistem|
:Tampilkan Daftar Tagihan;
|Siswa|
:Pilih Tagihan & Klik Bayar;
|Sistem|
:Request Token ke Midtrans;
|Midtrans Gateway|
:Return Token Pembayaran;
|Sistem|
:Tampilkan Popup Pembayaran;
|Siswa|
:Pilih Metode Bayar & Transfer;
|Midtrans Gateway|
:Verifikasi Pembayaran Sukses;
:Kirim Webhook ke Sistem;
|Sistem|
:Update Status Tagihan (Lunas);
:<< include >>\nGenerate Invoice;
|Siswa|
:<< include >>\nLihat Invoice;
stop
@enduml
"@
    },
    @{
        name = "8_Kerjakan_Ujian"
        code = @"
@startuml
$commonStyle
|Siswa|
start
:Buka Menu Ujian;
:Pilih Ujian yang Tersedia;
|Sistem|
:Validasi Waktu & Akses;
:Tampilkan Halaman Soal;
|Siswa|
:Jawab Pertanyaan Ujian;
:<< include >>\nKumpulkan Jawaban;
|Sistem|
:Simpan Jawaban ke Database;
if (Soal Pilihan Ganda?) then (Ya)
  :Hitung Nilai Otomatis;
  :Simpan Nilai Akhir;
else (Tidak / Essay)
  :Tandai "Menunggu Penilaian Guru";
endif
:Update Status Ujian Selesai;
|Siswa|
stop
@enduml
"@
    }
)

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

foreach ($diag in $diagrams) {
    Write-Host "Generating $($diag.name).png ..."
    try {
        Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $diag.code -ContentType "text/plain" -OutFile "c:\web\elearning\activity diagram\$($diag.name).png" -ErrorAction Stop
        Write-Host "Success!"
    } catch {
        Write-Host "Error generating $($diag.name): $_"
    }
    Start-Sleep -Seconds 2
}
Write-Host "Done!"
