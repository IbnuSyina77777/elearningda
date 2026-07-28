$commonStyle = @"
left to right direction
skinparam BackgroundColor white
skinparam DefaultFontName Arial
skinparam DefaultFontSize 14
skinparam Shadowing false
skinparam RoundCorner 10
skinparam usecase {
    BackgroundColor #e1f5fe
    BorderColor #0288d1
    BorderThickness 2
    ArrowColor #333333
    FontColor #000000
}
skinparam actor {
    BackgroundColor #f9f9f9
    BorderColor #333333
    BorderThickness 2
    FontColor #000000
}
skinparam rectangle {
    BorderColor #666666
    BackgroundColor transparent
    BorderThickness 2
    BorderStyle dashed
}
"@

$diagrams = @(
    @{
        name = "1_UC_Login_ke_Sistem"
        code = @"
@startuml
$commonStyle
actor "Semua Pengguna\n(Admin/Guru/Siswa)" as User
rectangle "Sistem E-Learning" {
    usecase "Login ke Sistem" as UC1
}
User -- UC1
@enduml
"@
    },
    @{
        name = "2_UC_Kelola_Data_Pengguna"
        code = @"
@startuml
$commonStyle
actor "Admin" as Admin
rectangle "Manajemen Pengguna" {
    usecase "Kelola Data Siswa" as UC1
    usecase "Kelola Data Guru" as UC2
}
Admin -- UC1
Admin -- UC2
@enduml
"@
    },
    @{
        name = "3_UC_Lihat_Laporan"
        code = @"
@startuml
$commonStyle
actor "Admin" as Admin
rectangle "Manajemen Laporan" {
    usecase "Lihat Laporan Keuangan" as UC1
    usecase "Lihat Laporan Akademik" as UC2
}
Admin -- UC1
Admin -- UC2
@enduml
"@
    },
    @{
        name = "4_UC_Buat_Materi_Pelajaran"
        code = @"
@startuml
$commonStyle
actor "Guru" as Guru
rectangle "Manajemen Akademik" {
    usecase "Buat Materi Pelajaran" as UC1
}
Guru -- UC1
@enduml
"@
    },
    @{
        name = "5_UC_Kelola_Tugas_Nilai"
        code = @"
@startuml
$commonStyle
actor "Guru" as Guru
rectangle "Manajemen Akademik" {
    usecase "Berikan Tugas / Ujian" as UC1
    usecase "Input Nilai Siswa" as UC2
}
Guru -- UC1
Guru -- UC2
@enduml
"@
    },
    @{
        name = "6_UC_Ikuti_Pelajaran"
        code = @"
@startuml
$commonStyle
actor "Siswa" as Siswa
rectangle "Ruang Belajar" {
    usecase "Lihat Jadwal Pelajaran" as UC1
    usecase "Akses Materi Belajar" as UC2
}
Siswa -- UC1
Siswa -- UC2
@enduml
"@
    },
    @{
        name = "7_UC_Proses_Pembayaran"
        code = @"
@startuml
$commonStyle
actor "Siswa" as Siswa
rectangle "Modul Keuangan" {
    usecase "Lihat Tagihan SPP" as UC1
    usecase "Bayar via Midtrans" as UC2
    usecase "Cetak Invoice" as UC3
}
Siswa -- UC1
Siswa -- UC2
UC2 .> UC3 : <<include>>
@enduml
"@
    },
    @{
        name = "8_UC_Kerjakan_Ujian"
        code = @"
@startuml
$commonStyle
actor "Siswa" as Siswa
rectangle "Ruang Belajar" {
    usecase "Kerjakan Soal Ujian" as UC1
    usecase "Kumpulkan Jawaban" as UC2
}
Siswa -- UC1
UC1 .> UC2 : <<include>>
@enduml
"@
    }
)

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

foreach ($diag in $diagrams) {
    Write-Host "Generating $($diag.name).png ..."
    try {
        Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $diag.code -ContentType "text/plain" -OutFile "c:\web\elearning\diagram 3\$($diag.name).png" -ErrorAction Stop
        Write-Host "Success!"
    } catch {
        Write-Host "Error generating $($diag.name): $_"
    }
    Start-Sleep -Seconds 1
}
Write-Host "Done!"
