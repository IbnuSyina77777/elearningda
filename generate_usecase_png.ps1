$usecaseCode = @"
@startuml
left to right direction
skinparam BackgroundColor white
skinparam DefaultFontName Arial
skinparam DefaultFontSize 14
skinparam Shadowing false
skinparam RoundCorner 10

skinparam rectangle {
    BorderColor #666666
    BackgroundColor transparent
    BorderThickness 2
    BorderStyle dashed
}

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

actor "Admin" as Admin
actor "Guru" as Guru
actor "Siswa" as Siswa

rectangle "Sistem E-Learning & Pembayaran" {
    usecase "Login ke Sistem" as UC_Login
    usecase "Kelola Data Pengguna" as UC_Users
    usecase "Lihat Laporan" as UC_Laporan
    usecase "Buat Materi Pelajaran" as UC_Materi
    usecase "Kelola Tugas & Nilai" as UC_Nilai
    usecase "Ikuti Pelajaran" as UC_Akses
    usecase "Kerjakan Ujian" as UC_Ujian
    usecase "Kumpulkan Jawaban" as UC_Submit
    usecase "Proses Pembayaran" as UC_Bayar
    usecase "Lihat Invoice" as UC_Invoice
}

Admin -- UC_Login
Guru -- UC_Login
Siswa -- UC_Login

Admin -- UC_Users
Admin -- UC_Laporan

Guru -- UC_Materi
Guru -- UC_Nilai

Siswa -- UC_Akses
Siswa -- UC_Ujian
Siswa -- UC_Bayar

UC_Ujian .> UC_Submit : <<include>>
UC_Bayar .> UC_Invoice : <<include>>
@enduml
"@

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
Write-Host "Generating usecase_drawio.png ..."
Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $usecaseCode -ContentType "text/plain" -OutFile "c:\web\elearning\usecase_drawio.png"
Write-Host "Done!"
