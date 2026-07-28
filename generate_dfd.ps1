$commonStyle = @"
skinparam BackgroundColor white
skinparam Shadowing false
skinparam DefaultFontName Arial

' External Entities (Kotak bersudut tajam)
skinparam rectangle {
    BackgroundColor #f9f9f9
    BorderColor #333333
    BorderThickness 2
    RoundCorner 0
}

' Processes (Lingkaran / Oval)
skinparam usecase {
    BackgroundColor #e1f5fe
    BorderColor #0288d1
    BorderThickness 2
}

' Datastores (Database symbol)
skinparam database {
    BackgroundColor #ffffff
    BorderColor #333333
    BorderThickness 2
}

' Data Flows
skinparam arrow {
    Color #333333
    Thickness 1.5
    FontSize 12
    FontColor #333333
}
"@

$diagrams = @(
    @{
        name = "DFD_Level_0_Context"
        code = @"
@startuml
$commonStyle
left to right direction

rectangle "Admin" as E1
rectangle "Guru" as E2
rectangle "Siswa" as E3

usecase "0\nSistem E-Learning" as P0

E1 -down-> P0 : Data Pengguna, Konfigurasi\nVerifikasi Pembayaran
P0 -up-> E1 : Laporan Keuangan, Log Sistem

E2 -right-> P0 : Materi Pelajaran, Soal Ujian,\nInput Nilai
P0 -left-> E2 : Data Siswa, Laporan Evaluasi

E3 -left-> P0 : Bukti Pembayaran, Jawaban Ujian
P0 -right-> E3 : Tagihan, Materi Pelajaran, Hasil Ujian
@enduml
"@
    },
    @{
        name = "DFD_Level_1"
        code = @"
@startuml
$commonStyle
left to right direction

' Entities
rectangle "Admin" as Admin
rectangle "Guru" as Guru
rectangle "Siswa" as Siswa

' Processes
usecase "1.0\nKelola Pengguna\n& Data Master" as P1
usecase "2.0\nKelola\nPembelajaran" as P2
usecase "3.0\nManajemen Ujian\n& Penilaian" as P3
usecase "4.0\nManajemen\nKeuangan" as P4

' Datastores
database "D1 Pengguna & Kelas" as D1
database "D2 Materi" as D2
database "D3 Ujian & Nilai" as D3
database "D4 Tagihan & Transaksi" as D4

' Flows for P1
Admin --> P1 : Data Siswa, Data Guru
P1 --> D1 : Simpan/Update Data
D1 --> P1 : Data Master
P1 --> Admin : Informasi Pengguna

' Flows for P2
Guru --> P2 : Upload Materi
P2 --> D2 : Simpan Materi
D2 --> P2 : Ambil Materi
P2 --> Siswa : Akses Materi

' Flows for P3
Guru --> P3 : Soal Ujian
Siswa --> P3 : Jawaban Ujian
P3 --> D3 : Simpan Hasil
D3 --> P3 : Ambil Nilai
P3 --> Siswa : Hasil Ujian
P3 --> Guru : Rekap Nilai Siswa

' Flows for P4
Siswa --> P4 : Input Pembayaran
P4 --> D4 : Catat Transaksi
D4 --> P4 : Status Tagihan
P4 --> Siswa : Invoice/Bukti
P4 --> Admin : Laporan Pemasukan

' Inter-process or Inter-datastore reference (optional)
D1 --> P2 : Data Kelas
D1 --> P3 : Data Siswa
D1 --> P4 : Data Siswa (Tagihan)
@enduml
"@
    }
)

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

foreach ($diag in $diagrams) {
    Write-Host "Generating $($diag.name).png ..."
    try {
        Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $diag.code -ContentType "text/plain" -OutFile "c:\web\elearning\diagram\$($diag.name).png" -ErrorAction Stop
        Write-Host "Success!"
    } catch {
        Write-Host "Error generating $($diag.name): $_"
    }
    Start-Sleep -Seconds 1
}
Write-Host "Done!"
