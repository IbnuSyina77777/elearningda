$commonStyle = @"
skinparam BackgroundColor white
skinparam ActivityBackgroundColor #e8f3fa
skinparam ActivityBorderColor #1c4a6b
skinparam ActivityDiamondBackgroundColor #fff2cc
skinparam ActivityDiamondBorderColor #d6b656
skinparam SwimlaneBorderColor #1c4a6b
skinparam ArrowColor #1c4a6b
skinparam DefaultFontName Arial
"@

$diagrams = @(
    @{
        name = "activity_login"
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
        name = "activity_admin_pengguna"
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
        name = "activity_guru_materi"
        code = @"
@startuml
$commonStyle
|Guru|
start
:Buka Kelas / Mata Pelajaran;
:Pilih Menu Tambah Materi/Tugas;
:Input Judul, Deskripsi & Lampiran;
:Klik Publish / Simpan;
|Sistem|
:Simpan Materi/Tugas;
:Kirim Notifikasi ke Siswa;
:Tampilkan Materi di Kelas;
|Guru|
stop
@enduml
"@
    },
    @{
        name = "activity_siswa_ujian"
        code = @"
@startuml
$commonStyle
|Siswa|
start
:Buka Menu Tugas / Ujian;
:Pilih Ujian yang Tersedia;
|Sistem|
:Tampilkan Halaman Soal;
|Siswa|
:Jawab Pertanyaan Ujian;
:Klik Kumpulkan;
|Sistem|
:Simpan Jawaban ke Database;
if (Soal Pilihan Ganda?) then (Ya)
  :Hitung Nilai Otomatis;
  :Simpan Nilai Akhir;
else (Tidak / Essay)
  :Tandai "Menunggu Penilaian Guru";
endif
:Update Status "Selesai Dikerjakan";
|Siswa|
stop
@enduml
"@
    },
    @{
        name = "activity_siswa_pembayaran"
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
:Generate Invoice;
|Siswa|
:Lihat & Download Invoice;
stop
@enduml
"@
    }
)

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

foreach ($diag in $diagrams) {
    Write-Host "Generating $($diag.name).png ..."
    try {
        Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $diag.code -ContentType "text/plain" -OutFile "c:\web\elearning\$($diag.name).png" -ErrorAction Stop
        Write-Host "Success!"
    } catch {
        Write-Host "Error generating $($diag.name): $_"
    }
    Start-Sleep -Seconds 2
}
Write-Host "Done!"
