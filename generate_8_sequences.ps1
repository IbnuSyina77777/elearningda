$commonStyle = @"
skinparam BackgroundColor white
skinparam SequenceLifeLineBorderColor #1c4a6b
skinparam SequenceParticipantBorderColor #1c4a6b
skinparam SequenceParticipantBackgroundColor #e1f5fe
skinparam SequenceActorBorderColor #333333
skinparam SequenceActorBackgroundColor #f9f9f9
skinparam SequenceArrowColor #333333
skinparam DefaultFontName Arial
skinparam DefaultFontSize 13
"@

$diagrams = @(
    @{
        name = "1_Seq_Login_ke_Sistem"
        code = @"
@startuml
$commonStyle
actor Pengguna
participant "Web UI" as UI
participant "Auth Controller" as Auth
database Database

Pengguna -> UI: Input Kredensial
UI -> Auth: Request Login(user, pass)
Auth -> Database: Query Validasi
Database --> Auth: Return User Data / Null
alt Validasi Sukses
    Auth -> UI: Return Sukses & Set Session
    UI --> Pengguna: Redirect ke Dashboard
else Validasi Gagal
    Auth -> UI: Return Error
    UI --> Pengguna: Tampilkan Pesan Error
end
@enduml
"@
    },
    @{
        name = "2_Seq_Kelola_Data_Pengguna"
        code = @"
@startuml
$commonStyle
actor Admin
participant "Web UI" as UI
participant "User Controller" as Controller
database Database

Admin -> UI: Buka Menu Pengguna
UI -> Controller: Request Data Pengguna
Controller -> Database: Fetch Users
Database --> Controller: Return Data
Controller --> UI: Render View
Admin -> UI: Input Data Pengguna Baru & Submit
UI -> Controller: POST Data Pengguna
Controller -> Database: Insert / Update
Database --> Controller: Success
Controller --> UI: Redirect & Flash Success
UI --> Admin: Tampilkan Notifikasi Sukses
@enduml
"@
    },
    @{
        name = "3_Seq_Lihat_Laporan"
        code = @"
@startuml
$commonStyle
actor Admin
participant "Web UI" as UI
participant "Report Controller" as Controller
database Database

Admin -> UI: Buka Menu Laporan
UI -> Controller: GET Laporan (Filter Waktu)
Controller -> Database: Query Laporan Terkait
Database --> Controller: Return Data Laporan
Controller -> Controller: Kalkulasi / Formatting
Controller --> UI: Render View Laporan
UI --> Admin: Tampilkan Tabel Laporan
Admin -> UI: Klik Download PDF/Excel
UI -> Controller: Request Export Laporan
Controller --> UI: Return File Blob
UI --> Admin: Download File Laporan
@enduml
"@
    },
    @{
        name = "4_Seq_Buat_Materi_Pelajaran"
        code = @"
@startuml
$commonStyle
actor Guru
participant "Web UI" as UI
participant "Material Controller" as Controller
database Database

Guru -> UI: Buka Kelas & Klik Tambah Materi
Guru -> UI: Isi Form (Judul, Konten, File) & Publish
UI -> Controller: POST Data Materi (File Upload)
Controller -> Controller: Simpan File ke Storage
Controller -> Database: Insert Record Materi
Database --> Controller: Success
Controller -> Controller: Trigger Notifikasi (Optional)
Controller --> UI: Redirect ke Detail Kelas
UI --> Guru: Tampilkan Materi Baru Tersimpan
@enduml
"@
    },
    @{
        name = "5_Seq_Kelola_Tugas_Nilai"
        code = @"
@startuml
$commonStyle
actor Guru
participant "Web UI" as UI
participant "Grade Controller" as Controller
database Database

Guru -> UI: Buka Daftar Tugas Siswa
UI -> Controller: Request Jawaban Siswa
Controller -> Database: Fetch Submissions
Database --> Controller: Return Data
Controller --> UI: Render Daftar Jawaban
Guru -> UI: Review Jawaban & Input Nilai
UI -> Controller: POST Nilai & Feedback
Controller -> Database: Update Grade Record
Database --> Controller: Success
Controller --> UI: Redirect dengan Pesan Sukses
UI --> Guru: Tampilkan Perubahan Nilai
@enduml
"@
    },
    @{
        name = "6_Seq_Ikuti_Pelajaran"
        code = @"
@startuml
$commonStyle
actor Siswa
participant "Web UI" as UI
participant "Course Controller" as Controller
database Database

Siswa -> UI: Buka Dashboard & Pilih Kelas
UI -> Controller: Request Materi Kelas
Controller -> Database: Fetch Class Materials
Database --> Controller: Return Data
Controller --> UI: Render View Materi
Siswa -> UI: Klik Modul / Video Pelajaran
UI -> Controller: Track Log Akses (Optional)
Controller -> Database: Simpan Log Akses
Controller --> UI: Tampilkan Konten Penuh
UI --> Siswa: Tampilkan Teks & Media
Siswa -> UI: Download Lampiran File
@enduml
"@
    },
    @{
        name = "7_Seq_Proses_Pembayaran"
        code = @"
@startuml
$commonStyle
actor Siswa
participant "Web UI" as UI
participant "Payment Controller" as PC
participant "Midtrans API" as Midtrans
database Database

Siswa -> UI: Buka Menu Tagihan
UI -> PC: Get Tagihan Unpaid
PC -> Database: Fetch Bills
PC --> UI: Render Tagihan
Siswa -> UI: Klik "Bayar Sekarang"
UI -> PC: POST Request Pembayaran
PC -> Midtrans: Create Snap Token Request
Midtrans --> PC: Return Snap Token
PC --> UI: Load Midtrans Snap UI
Siswa -> UI: Pilih Metode & Transfer
Midtrans -> PC: Webhook Notification (Settlement)
PC -> Database: Update Tagihan (Status: Paid)
PC --> Midtrans: 200 OK
Siswa -> UI: Cek Tagihan Lunas
UI -> PC: Get Status
PC --> UI: Render Invoice
UI --> Siswa: Tampilkan / Download Invoice
@enduml
"@
    },
    @{
        name = "8_Seq_Kerjakan_Ujian"
        code = @"
@startuml
$commonStyle
actor Siswa
participant "Web UI" as UI
participant "Exam Controller" as Controller
database Database

Siswa -> UI: Buka Halaman Ujian
UI -> Controller: Validasi Waktu Ujian
Controller -> Database: Cek Jadwal & Akses
Database --> Controller: OK
Controller --> UI: Tampilkan Form Soal
Siswa -> UI: Jawab & Klik Kumpulkan
UI -> Controller: POST Jawaban Ujian
Controller -> Database: Simpan Submission
alt Pilihan Ganda
    Controller -> Controller: Hitung Skor Otomatis
    Controller -> Database: Update Final Grade
end
Database --> Controller: Success
Controller --> UI: Redirect ke Halaman Selesai
UI --> Siswa: Tampilkan Status "Telah Mengerjakan"
@enduml
"@
    }
)

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

foreach ($diag in $diagrams) {
    Write-Host "Generating $($diag.name).png ..."
    try {
        Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $diag.code -ContentType "text/plain" -OutFile "c:\web\elearning\sequence diagram\$($diag.name).png" -ErrorAction Stop
        Write-Host "Success!"
    } catch {
        Write-Host "Error generating $($diag.name): $_"
    }
    Start-Sleep -Seconds 2
}
Write-Host "Done!"
