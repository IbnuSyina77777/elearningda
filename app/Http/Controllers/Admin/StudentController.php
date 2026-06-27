<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'classroom.major'])
            ->where('status', '!=', 'alumni');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('classroom_id') && $request->classroom_id != '') {
            $query->where('classroom_id', $request->classroom_id);
        }

        $students = $query->latest()->paginate(15)->withQueryString();
        $classrooms = Classroom::with('major')->get();

        return view('admin.students.index', compact('students', 'classrooms'));
    }

    public function create()
    {
        $classrooms = Classroom::with('major')->get();
        return view('admin.students.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8',
            'nisn'         => 'required|string|unique:students,nisn',
            'nis'          => 'required|string|unique:students,nis',
            'classroom_id' => 'required|exists:classrooms,id',
            'gender'       => 'required|in:L,P',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string',
            'photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'parent_name'  => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'student',
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students/photos', 'public');
            }

            Student::create([
                'user_id'      => $user->id,
                'classroom_id' => $validated['classroom_id'],
                'nisn'         => $validated['nisn'],
                'nis'          => $validated['nis'],
                'gender'       => $validated['gender'],
                'phone'        => $validated['phone'],
                'address'      => $validated['address'],
                'photo'        => $photoPath,
                'parent_name'  => $validated['parent_name'],
                'parent_phone' => $validated['parent_phone'],
            ]);

            DB::commit();

            return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Student $student)
    {
        $student->load('user');
        $classrooms = Classroom::with('major')->get();
        
        return view('admin.students.edit', compact('student', 'classrooms'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $student->user_id,
            'password'     => 'nullable|string|min:8',
            'nisn'         => 'required|string|unique:students,nisn,' . $student->id,
            'nis'          => 'required|string|unique:students,nis,' . $student->id,
            'classroom_id' => 'nullable|exists:classrooms,id',
            'gender'       => 'required|in:L,P',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string',
            'photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'parent_name'  => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $userData = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $student->user->update($userData);

            $studentData = [
                'classroom_id' => $validated['classroom_id'] ?? null,
                'status'       => empty($validated['classroom_id']) ? 'alumni' : 'active',
                'nisn'         => $validated['nisn'],
                'nis'          => $validated['nis'],
                'gender'       => $validated['gender'],
                'phone'        => $validated['phone'],
                'address'      => $validated['address'],
                'parent_name'  => $validated['parent_name'],
                'parent_phone' => $validated['parent_phone'],
            ];

            if ($request->hasFile('photo')) {
                // Hapus foto lama jika ada
                if ($student->photo && \Storage::disk('public')->exists($student->photo)) {
                    \Storage::disk('public')->delete($student->photo);
                }
                $studentData['photo'] = $request->file('photo')->store('students/photos', 'public');
            }

            $student->update($studentData);

            DB::commit();

            return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();
            
            // 1. Ambil semua ID tagihan milik siswa ini (termasuk yang soft-deleted)
            $billIds = $student->bills()->withTrashed()->pluck('id');
            
            // 2. Hapus permanen semua transaksi yang terkait dengan tagihan-tagihan ini
            \App\Models\Transaction::whereIn('bill_id', $billIds)->delete();
            
            // 3. Hapus permanen semua tagihan
            $student->bills()->withTrashed()->forceDelete();

            $user = $student->user;
            
            // 4. Hapus permanen data siswa agar foreign key constraint di tabel users aman
            $student->forceDelete();
            
            // 5. Hapus data akun login
            if ($user) {
                $user->delete();
            }
            
            DB::commit();
            return redirect()->route('admin.students.index')->with('success', 'Data siswa beserta seluruh riwayat tagihannya berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = Student::with(['user', 'classroom.major']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('classroom_id') && $request->classroom_id != '') {
            $query->where('classroom_id', $request->classroom_id);
        }

        $students = $query->get();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StudentsExport($students), 'Data_Siswa_' . date('Ymd_His') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $file = $request->file('file');
        
        try {
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\StudentsImport, $file)[0];
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca fail Excel. Pastikan formatnya benar. Error: ' . $e->getMessage());
        }
        
        if (count($rows) === 0) {
            return back()->with('error', 'Fail Excel kosong.');
        }

        $header = array_shift($rows);
        
        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];
        
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Skip completely empty rows without counting as error
                $isEmpty = true;
                foreach ($row as $cell) {
                    if (trim($cell ?? '') !== '') {
                        $isEmpty = false;
                        break;
                    }
                }
                if ($isEmpty) continue;

                $rowNumber = $index + 2; // +1 for 0-index, +1 for header

                // ['NISN', 'NIS', 'Nama Lengkap', 'Email', 'Jenis Kelamin', 'Telepon', 'Alamat', 'Nama Orang Tua', 'Telepon Orang Tua', 'Kelas']
                $nisn = trim($row[0] ?? '');
                $nis = trim($row[1] ?? '');
                $name = trim($row[2] ?? '');
                $email = trim($row[3] ?? '');
                $gender = trim($row[4] ?? '');
                $gender = ($gender === 'L' || $gender === 'P') ? $gender : 'L';
                $phone = trim($row[5] ?? '');
                $address = trim($row[6] ?? '');
                $parent_name = trim($row[7] ?? '');
                $parent_phone = trim($row[8] ?? '');
                $classroom_name = trim($row[9] ?? '');

                if (empty($nisn) || empty($nis) || empty($name) || empty($email) || empty($classroom_name)) {
                    $errorCount++;
                    $errorMessages[] = "Baris {$rowNumber}: Data wajib (NISN/NIS/Nama/Email/Kelas) ada yang kosong.";
                    continue;
                }

                // Cek apakah NISN/NIS/Email sudah ada
                if (Student::where('nisn', $nisn)->exists()) {
                    $errorCount++;
                    $errorMessages[] = "Baris {$rowNumber}: NISN {$nisn} sudah terdaftar.";
                    continue;
                }
                if (Student::where('nis', $nis)->exists()) {
                    $errorCount++;
                    $errorMessages[] = "Baris {$rowNumber}: NIS {$nis} sudah terdaftar.";
                    continue;
                }
                if (User::where('email', $email)->exists()) {
                    $errorCount++;
                    $errorMessages[] = "Baris {$rowNumber}: Email {$email} sudah digunakan.";
                    continue;
                }

                $classroom = Classroom::where('name', $classroom_name)->first();
                if (!$classroom) {
                    $errorCount++;
                    $errorMessages[] = "Baris {$rowNumber}: Kelas '{$classroom_name}' tidak ditemukan di sistem.";
                    continue;
                }

                $user = User::create([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => Hash::make($nisn), // Default password adalah NISN
                    'role'     => 'student',
                ]);

                Student::create([
                    'user_id'      => $user->id,
                    'classroom_id' => $classroom->id,
                    'nisn'         => $nisn,
                    'nis'          => $nis,
                    'gender'       => $gender,
                    'phone'        => $phone,
                    'address'      => $address,
                    'parent_name'  => $parent_name,
                    'parent_phone' => $parent_phone,
                ]);
                
                $successCount++;
            }
            DB::commit();

            $msg = "Import selesai. Berhasil: {$successCount}, Gagal/Dilewati: {$errorCount}";
            if ($errorCount > 0 && count($errorMessages) > 0) {
                return redirect()->route('admin.students.index')
                    ->with('warning', $msg . '. Beberapa baris dilewati karena ada kesalahan (lihat detail).')
                    ->with('import_errors', array_slice($errorMessages, 0, 15));
            }

            return redirect()->route('admin.students.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
