<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\Classroom;
use App\Models\Major;
use App\Models\PaymentCategory;
use App\Models\PaymentItem;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================================
        // 1. Admin User
        // =====================================================================
        $admin = User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@smk-elearning.test',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // =====================================================================
        // 2. Jurusan (Majors)
        // =====================================================================
        $majors = collect([
            ['code' => 'TKJ',  'name' => 'Teknik Komputer Jaringan'],
            ['code' => 'TBSM', 'name' => 'Teknik Bisnis Sepeda Motor'],
            ['code' => 'AKL',  'name' => 'Akuntansi dan Keuangan Lembaga'],
        ])->map(fn ($data) => Major::create($data));

        // =====================================================================
        // 3. Kelas (Classrooms) — 2 kelas per jurusan per tingkat
        // =====================================================================
        $classrooms = collect();
        foreach ($majors as $major) {
            foreach (['X', 'XI', 'XII'] as $level) {
                for ($i = 1; $i <= 2; $i++) {
                    $classrooms->push(Classroom::create([
                        'major_id' => $major->id,
                        'name'     => "{$level}-{$major->code}-{$i}",
                        'level'    => $level,
                    ]));
                }
            }
        }

        // =====================================================================
        // 4. Tahun Ajaran (Academic Years)
        // =====================================================================
        $academicYears = collect([
            [
                'name'       => '2025/2026',
                'semester'   => 'Ganjil',
                'start_date' => '2025-07-14',
                'end_date'   => '2025-12-20',
                'is_active'  => false,
            ],
            [
                'name'       => '2025/2026',
                'semester'   => 'Genap',
                'start_date' => '2026-01-05',
                'end_date'   => '2026-06-20',
                'is_active'  => true,
            ],
        ])->map(fn ($data) => AcademicYear::create($data));

        $activeYear = $academicYears->last();

        // =====================================================================
        // 5. Kategori Pembayaran (Payment Categories)
        // =====================================================================
        $categories = collect([
            [
                'code'           => 'PTS',
                'name'           => 'Penilaian Tengah Semester',
                'description'    => 'Biaya pelaksanaan Penilaian Tengah Semester (PTS)',
                'default_amount' => 150000,
            ],
            [
                'code'           => 'PAS',
                'name'           => 'Penilaian Akhir Semester',
                'description'    => 'Biaya pelaksanaan Penilaian Akhir Semester (PAS)',
                'default_amount' => 200000,
            ],
            [
                'code'           => 'UJIKOM',
                'name'           => 'Uji Kompetensi Keahlian',
                'description'    => 'Biaya pelaksanaan Uji Kompetensi Keahlian (Ujikom) untuk siswa kelas XII',
                'default_amount' => 500000,
            ],
            [
                'code'           => 'KUNJIND',
                'name'           => 'Kunjungan Industri',
                'description'    => 'Biaya pelaksanaan Kunjungan Industri ke perusahaan mitra',
                'default_amount' => 350000,
            ],
        ])->map(fn ($data) => PaymentCategory::create($data));

        // =====================================================================
        // 6. Siswa + User Accounts (5 sample students)
        // =====================================================================
        $studentData = [
            ['name' => 'Ahmad Fadillah',    'nisn' => '0012345601', 'nis' => 'SMK2025001', 'gender' => 'L', 'classroom_index' => 0],
            ['name' => 'Siti Nurhaliza',    'nisn' => '0012345602', 'nis' => 'SMK2025002', 'gender' => 'P', 'classroom_index' => 1],
            ['name' => 'Budi Santoso',      'nisn' => '0012345603', 'nis' => 'SMK2025003', 'gender' => 'L', 'classroom_index' => 2],
            ['name' => 'Dewi Sartika',      'nisn' => '0012345604', 'nis' => 'SMK2025004', 'gender' => 'P', 'classroom_index' => 3],
            ['name' => 'Rizky Pratama',     'nisn' => '0012345605', 'nis' => 'SMK2025005', 'gender' => 'L', 'classroom_index' => 4],
        ];

        $students = collect();
        foreach ($studentData as $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => strtolower(str_replace(' ', '.', $data['name'])) . '@student.smk.test',
                'password' => Hash::make('password'),
                'role'     => 'student',
            ]);

            $students->push(Student::create([
                'user_id'      => $user->id,
                'classroom_id' => $classrooms[$data['classroom_index']]->id,
                'nisn'         => $data['nisn'],
                'nis'          => $data['nis'],
                'gender'       => $data['gender'],
                'phone'        => '0812' . rand(10000000, 99999999),
                'parent_name'  => 'Orang Tua ' . $data['name'],
                'parent_phone' => '0813' . rand(10000000, 99999999),
            ]));
        }

        // =====================================================================
        // 7. Tagihan (Bills) — setiap siswa mendapat PTS + PAS
        // =====================================================================
        $pts = $categories->firstWhere('code', 'PTS');
        $pas = $categories->firstWhere('code', 'PAS');

        foreach ($students as $student) {
            // Bill PTS
            $billPts = Bill::create([
                'student_id'          => $student->id,
                'payment_category_id' => $pts->id,
                'academic_year_id'    => $activeYear->id,
                'amount'              => $pts->default_amount,
                'status'              => 'unpaid',
                'due_date'            => '2026-03-15',
            ]);

            // Bill PAS
            $billPas = Bill::create([
                'student_id'          => $student->id,
                'payment_category_id' => $pas->id,
                'academic_year_id'    => $activeYear->id,
                'amount'              => $pas->default_amount,
                'status'              => 'unpaid',
                'due_date'            => '2026-06-10',
            ]);

            // Contoh cicilan untuk PAS (2 termin)
            PaymentItem::create([
                'bill_id'            => $billPas->id,
                'installment_number' => 1,
                'amount'             => 100000,
                'status'             => 'unpaid',
                'due_date'           => '2026-04-15',
            ]);

            PaymentItem::create([
                'bill_id'            => $billPas->id,
                'installment_number' => 2,
                'amount'             => 100000,
                'status'             => 'unpaid',
                'due_date'           => '2026-05-15',
            ]);
        }

        // Siswa pertama: tambah Ujikom + Kunjungan Industri (kelas XII)
        $ujikom  = $categories->firstWhere('code', 'UJIKOM');
        $kunjind = $categories->firstWhere('code', 'KUNJIND');

        Bill::create([
            'student_id'          => $students->first()->id,
            'payment_category_id' => $ujikom->id,
            'academic_year_id'    => $activeYear->id,
            'amount'              => $ujikom->default_amount,
            'status'              => 'unpaid',
            'due_date'            => '2026-02-28',
        ]);

        Bill::create([
            'student_id'          => $students->first()->id,
            'payment_category_id' => $kunjind->id,
            'academic_year_id'    => $activeYear->id,
            'amount'              => $kunjind->default_amount,
            'status'              => 'unpaid',
            'due_date'            => '2026-04-30',
        ]);

        // =====================================================================
        // Summary
        // =====================================================================
        $this->command->info('✅ Seeder berhasil dijalankan!');
        $this->command->info("   - {$majors->count()} jurusan");
        $this->command->info("   - {$classrooms->count()} kelas");
        $this->command->info("   - {$academicYears->count()} tahun ajaran");
        $this->command->info("   - {$categories->count()} kategori pembayaran");
        $this->command->info("   - 1 admin + {$students->count()} siswa");
        $this->command->info("   - " . Bill::count() . " tagihan");
        $this->command->info("   - " . PaymentItem::count() . " item cicilan");
    }
}
