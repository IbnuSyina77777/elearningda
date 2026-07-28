# Perancangan Database (Kamus Data)
**Sistem E-Learning & Pembayaran**

Berikut adalah penjabaran struktur tabel (Kamus Data) beserta tipe data dan relasinya yang menjadi landasan untuk ERD (Entity Relationship Diagram) aplikasi ini.

## 1. Tabel `users`
Menyimpan data autentikasi dan profil dasar untuk semua pengguna (Admin, Guru, Siswa).
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `name` | Varchar(255) | Nama lengkap pengguna |
| `email` | Varchar(255) | Email (Unik) untuk login |
| `password` | Varchar(255) | Password (Hashed) |
| `role` | Enum | Pilihan: 'admin', 'teacher', 'student' |
| `created_at` | Timestamp | Waktu data dibuat |
| `updated_at` | Timestamp | Waktu data diupdate |

## 2. Tabel `students`
Menyimpan data spesifik siswa, berelasi *One-to-One* dengan tabel `users`.
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `user_id` | BigInt (FK) | Relasi ke `users.id` |
| `classroom_id` | BigInt (FK) | Relasi ke `classrooms.id` |
| `nisn` | Varchar(50) | Nomor Induk Siswa Nasional |
| `phone` | Varchar(20) | Nomor Telepon / WA |

## 3. Tabel `teachers`
Menyimpan data spesifik guru, berelasi *One-to-One* dengan tabel `users`.
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `user_id` | BigInt (FK) | Relasi ke `users.id` |
| `nip` | Varchar(50) | Nomor Induk Pegawai |
| `phone` | Varchar(20) | Nomor Telepon / WA |

## 4. Tabel `majors` (Jurusan)
Menyimpan referensi jurusan (misal: IPA, IPS, RPL, TKJ).
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `name` | Varchar(100) | Nama Jurusan |

## 5. Tabel `classrooms` (Kelas)
Menyimpan data rombongan belajar (kelas).
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `major_id` | BigInt (FK) | Relasi ke `majors.id` |
| `name` | Varchar(100) | Nama Kelas (misal: X-RPL 1) |
| `grade` | Integer | Tingkat Kelas (misal: 10, 11, 12) |

## 6. Tabel `subjects` (Mata Pelajaran)
Menyimpan daftar mata pelajaran.
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `name` | Varchar(150) | Nama Mata Pelajaran |

## 7. Tabel `schedules` (Jadwal Mengajar)
Tabel *pivot* atau transaksi yang menghubungkan Guru, Kelas, dan Mata Pelajaran dalam satu jadwal.
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `classroom_id` | BigInt (FK) | Relasi ke `classrooms.id` |
| `subject_id` | BigInt (FK) | Relasi ke `subjects.id` |
| `teacher_id` | BigInt (FK) | Relasi ke `teachers.id` |
| `day` | Varchar(20) | Hari (Senin, Selasa, dll) |
| `start_time` | Time | Jam mulai pelajaran |
| `end_time` | Time | Jam selesai pelajaran |

## 8. Tabel `materials` (Materi)
Menyimpan data file atau tautan materi yang diunggah oleh guru pada suatu jadwal/kelas.
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `schedule_id` | BigInt (FK) | Relasi ke `schedules.id` |
| `title` | Varchar(255) | Judul Materi |
| `file_path` | Varchar(255) | Path file materi di server |

## 9. Tabel `assignments` (Tugas/Ujian)
Menyimpan data tugas atau ujian beserta tenggat waktunya.
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `schedule_id` | BigInt (FK) | Relasi ke `schedules.id` |
| `title` | Varchar(255) | Judul Tugas/Ujian |
| `due_date` | DateTime | Batas Waktu Pengumpulan |

## 10. Tabel `submissions` (Pengumpulan Tugas/Nilai)
Menyimpan history pengumpulan jawaban siswa sekaligus menyimpan nilai (Grade) yang diberikan guru.
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `assignment_id`| BigInt (FK) | Relasi ke `assignments.id` |
| `student_id` | BigInt (FK) | Relasi ke `students.id` |
| `file_path` | Varchar(255) | Path file jawaban siswa |
| `grade` | Float | Nilai (0-100) |

## 11. Tabel `bills` (Tagihan)
Menyimpan data tagihan (Invoice) yang dibebankan kepada siswa (misal: SPP bulanan).
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `student_id` | BigInt (FK) | Relasi ke `students.id` |
| `amount` | Decimal(15,2) | Total tagihan |
| `status` | Enum | Pilihan: 'unpaid', 'paid' |

## 12. Tabel `transactions` (Transaksi Pembayaran Midtrans)
Menyimpan *log* pembayaran dan hasil *callback/webhook* dari Payment Gateway (Midtrans).
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `bill_id` | BigInt (FK) | Relasi ke `bills.id` |
| `order_id` | Varchar(100) | Order ID unik Midtrans |
| `payment_type` | Varchar(50) | Metode bayar (gopay, bank_transfer) |
| `status` | Varchar(50) | Status transaksi (settlement, pending) |

---

## Relasi Antar Tabel (Relationship Mapping)

Berikut adalah pemetaan relasi antar tabel beserta kardinalitasnya:

| No | Tabel Induk | Relasi | Tabel Anak | Keterangan |
| :---: | :--- | :---: | :--- | :--- |
| 1 | `users` | 1 → 1 | `students` | Satu user (role=student) memiliki satu data siswa |
| 2 | `users` | 1 → 1 | `teachers` | Satu user (role=teacher) memiliki satu data guru |
| 3 | `majors` | 1 → N | `classrooms` | Satu jurusan memiliki banyak kelas |
| 4 | `classrooms` | 1 → N | `students` | Satu kelas memiliki banyak siswa |
| 5 | `classrooms` | 1 → N | `schedules` | Satu kelas memiliki banyak jadwal |
| 6 | `subjects` | 1 → N | `schedules` | Satu mata pelajaran dijadwalkan berkali-kali |
| 7 | `teachers` | 1 → N | `schedules` | Satu guru mengajar di banyak jadwal |
| 8 | `schedules` | 1 → N | `materials` | Satu jadwal punya banyak materi |
| 9 | `schedules` | 1 → N | `assignments` | Satu jadwal punya banyak tugas/ujian |
| 10 | `assignments` | 1 → N | `submissions` | Satu tugas punya banyak pengumpulan |
| 11 | `students` | 1 → N | `submissions` | Satu siswa punya banyak pengumpulan |
| 12 | `students` | 1 → N | `bills` | Satu siswa punya banyak tagihan |
| 13 | `bills` | 1 → N | `transactions` | Satu tagihan bisa punya banyak percobaan transaksi |

---

## Catatan Perancangan Fisik (Physical Design Notes)

1. **Primary Key**: Semua tabel menggunakan `id` bertipe `BigInt` dengan `AUTO_INCREMENT`.
2. **Foreign Key Constraints**: Semua kolom `FK` dikenakan constraint `ON DELETE CASCADE` atau `ON DELETE SET NULL` sesuai kebutuhan bisnis.
3. **Indexing**: Kolom-kolom yang sering digunakan untuk pencarian (seperti `email`, `nisn`, `nip`, `order_id`) diberi index untuk mempercepat query.
4. **Soft Delete**: Tabel-tabel kritis (seperti `users`, `students`, `transactions`) menggunakan fitur *Soft Delete* (`deleted_at`) dari Laravel agar data tidak benar-benar terhapus.
5. **Timestamps**: Semua tabel memiliki kolom `created_at` dan `updated_at` yang dikelola otomatis oleh Laravel Eloquent ORM.
6. **DBMS**: MySQL 8.0+ (dengan engine InnoDB untuk mendukung transaksi dan *Foreign Key*).
