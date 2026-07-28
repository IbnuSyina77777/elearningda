$diagrams = @(
    @{
        name = "1_Mockup_Login"
        code = @"
@startsalt
{+
  <b>Sistem E-Learning & Pembayaran
  --
  Silakan Login ke Akun Anda
  --
  Username: | "                 "
  Password: | "                 "
  [   Login   ]
}
@endsalt
"@
    },
    @{
        name = "2_Mockup_Admin_Dashboard"
        code = @"
@startsalt
{+
  {* File | Master | Akademik | Keuangan | Laporan }
  --
  <b>Selamat Datang, Admin!
  --
  {
    Total Siswa: 500
    Total Guru: 45
    Kelas Aktif: 15
  }
  --
  [ Kelola Pengguna ] | [ Lihat Laporan Keuangan ]
}
@endsalt
"@
    },
    @{
        name = "3_Mockup_Admin_KelolaPengguna"
        code = @"
@startsalt
{+
  {* File | Master | Akademik | Keuangan | Laporan }
  --
  <b>Data Siswa
  [+ Tambah Siswa]
  --
  {#
    No | NISN | Nama | Kelas | Aksi
    1 | 1001 | Budi | X-A | [Edit] [Hapus]
    2 | 1002 | Siti | X-A | [Edit] [Hapus]
    3 | 1003 | Joko | X-B | [Edit] [Hapus]
  }
}
@endsalt
"@
    },
    @{
        name = "4_Mockup_Guru_Dashboard"
        code = @"
@startsalt
{+
  {* Dashboard | Jadwal | Tugas | Penilaian }
  --
  <b>Jadwal Mengajar Hari Ini
  --
  {#
    Jam | Kelas | Mata Pelajaran | Aksi
    07:00 | X-A | Matematika | [Buka Kelas]
    10:00 | X-B | Matematika | [Buka Kelas]
  }
}
@endsalt
"@
    },
    @{
        name = "5_Mockup_Guru_KelolaMateri"
        code = @"
@startsalt
{+
  {* Dashboard | Jadwal | Tugas | Penilaian }
  --
  <b>Materi: Matematika Kelas X-A
  [+ Tambah Materi/Tugas]
  --
  {#
    Tipe | Judul | Tanggal | Aksi
    [M] | Aljabar Linear | 12-Okt | [Lihat]
    [T] | Tugas Aljabar | 15-Okt | [Nilai(30)]
  }
}
@endsalt
"@
    },
    @{
        name = "6_Mockup_Siswa_Dashboard"
        code = @"
@startsalt
{+
  {* Dashboard | Ruang Belajar | Jadwal | Keuangan }
  --
  <b>Halo, Budi (Siswa)
  --
  {+
    <b>Tugas & Ujian Mendatang
    --
    [!] Ujian Matematika (Besok)
    [!] Tugas Biologi (Lusa)
  }
  {+
    <b>Status Tagihan
    --
    SPP Bulan Oktober: Rp 500.000 [Bayar Sekarang]
  }
}
@endsalt
"@
    },
    @{
        name = "7_Mockup_Siswa_Ujian"
        code = @"
@startsalt
{+
  <b>Ujian: Matematika Aljabar
  Sisa Waktu: 45:00
  --
  1. Berapakah nilai X jika 2X + 5 = 15?
  () 2
  (X) 5
  () 10
  () 15
  --
  2. Jelaskan konsep variabel dalam Aljabar!
  {S
    Variabel adalah simbol pengganti...
    ...
  }
  --
  [ Sebelumnya ] | [ Selanjutnya ]
  --
  [ Kumpulkan Jawaban (Submit) ]
}
@endsalt
"@
    },
    @{
        name = "8_Mockup_Siswa_Pembayaran"
        code = @"
@startsalt
{+
  {* Dashboard | Ruang Belajar | Jadwal | Keuangan }
  --
  <b>Daftar Tagihan Anda
  --
  {#
    Bulan | Nominal | Status | Aksi
    Agustus | Rp 500k | Lunas | [Invoice]
    September | Rp 500k | Lunas | [Invoice]
    Oktober | Rp 500k | Belum | [Bayar via Midtrans]
  }
  --
  <b>Popup Midtrans (Contoh)
  {+
    Total: Rp 500.000
    --
    Pilih Metode Bayar:
    ^Gopay / QRIS^
    [ Bayar Sekarang ]
  }
}
@endsalt
"@
    }
)

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

foreach ($diag in $diagrams) {
    Write-Host "Generating $($diag.name).png ..."
    try {
        Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $diag.code -ContentType "text/plain" -OutFile "c:\web\elearning\mockup_ui\$($diag.name).png" -ErrorAction Stop
        Write-Host "Success!"
    } catch {
        Write-Host "Error generating $($diag.name): $_"
    }
    Start-Sleep -Seconds 2
}
Write-Host "Done!"
