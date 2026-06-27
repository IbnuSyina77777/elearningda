<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'homeroomClass', 'taughtClassrooms']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $teachers = $query->orderBy('nip')->get();

        $groupedTeachers = [
            'X' => collect(),
            'XI' => collect(),
            'XII' => collect(),
            'Lainnya' => collect(),
        ];

        foreach ($teachers as $teacher) {
            $levels = $teacher->taughtClassrooms->pluck('level')->unique()->toArray();
            
            // Jika dia Wali Kelas tapi belum punya taughtClassrooms (misal data lama), coba cek homeroomClass
            if (empty($levels) && $teacher->homeroomClass) {
                $levels[] = $teacher->homeroomClass->level;
            }

            if (empty($levels)) {
                $groupedTeachers['Lainnya']->push($teacher);
            } else {
                foreach (array_unique($levels) as $level) {
                    if (isset($groupedTeachers[$level])) {
                        $groupedTeachers[$level]->push($teacher);
                    }
                }
            }
        }

        return view('admin.teachers.index', compact('groupedTeachers'));
    }

    public function create()
    {
        $classrooms = Classroom::orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $majors = \App\Models\Major::active()->orderBy('name')->get();
        return view('admin.teachers.create', compact('classrooms', 'subjects', 'majors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8',
            'nip'            => 'required|string|unique:teachers,nip',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'specialization' => 'nullable|string|max:255',
            'position'       => 'nullable|array',
            'position.*'     => 'string|max:255',
            'classroom_id'   => 'nullable|exists:classrooms,id',
            'taught_classes' => 'nullable|array',
            'taught_classes.*'=> 'exists:classrooms,id',
            'taught_subjects'=> 'nullable|array',
            'taught_subjects.*'=> 'exists:subjects,id',
            'major_id'       => 'nullable|exists:majors,id',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'teacher',
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('teachers/photos', 'public');
            }

            $teacher = Teacher::create([
                'user_id'        => $user->id,
                'nip'            => $validated['nip'],
                'phone'          => $validated['phone'],
                'address'        => $validated['address'],
                'photo'          => $photoPath,
                'specialization' => $validated['specialization'],
                'position'       => $validated['position'] ?? ['Guru Mata Pelajaran'],
                'classroom_id'   => in_array('Wali Kelas', $validated['position'] ?? []) ? $validated['classroom_id'] : null,
                'major_id'       => (in_array('Kepala Program Keahlian (Kajur)', $validated['position'] ?? []) || in_array('Kepala Bengkel / Laboratorium', $validated['position'] ?? [])) ? $validated['major_id'] : null,
            ]);

            $nonTeaching = ['Kepala Sekolah', 'Wakasek Kurikulum', 'Wakasek Kesiswaan', 'Wakasek Hubin / Humas', 'Wakasek Sarana Prasarana', 'Staf Tata Usaha (TU)', 'Pustakawan', 'Kepala Bengkel / Laboratorium', 'Kepala Program Keahlian (Kajur)'];
            $positions = $validated['position'] ?? ['Guru Mata Pelajaran'];
            $hasTeachingPosition = count(array_diff($positions, $nonTeaching)) > 0;
            if ($hasTeachingPosition) {
                if (!empty($validated['taught_classes'])) {
                    $teacher->taughtClassrooms()->sync($validated['taught_classes']);
                }
                if (!empty($validated['taught_subjects'])) {
                    $teacher->taughtSubjects()->sync($validated['taught_subjects']);
                }
            }

            DB::commit();
            return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user', 'homeroomClass', 'taughtClassrooms', 'taughtSubjects');
        $classrooms = Classroom::orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $majors = \App\Models\Major::active()->orderBy('name')->get();
        return view('admin.teachers.edit', compact('teacher', 'classrooms', 'subjects', 'majors'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $teacher->user_id,
            'password'       => 'nullable|string|min:8',
            'nip'            => 'required|string|unique:teachers,nip,' . $teacher->id,
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'specialization' => 'nullable|string|max:255',
            'position'       => 'nullable|array',
            'position.*'     => 'string|max:255',
            'classroom_id'   => 'nullable|exists:classrooms,id',
            'taught_classes' => 'nullable|array',
            'taught_classes.*'=> 'exists:classrooms,id',
            'taught_subjects'=> 'nullable|array',
            'taught_subjects.*'=> 'exists:subjects,id',
            'major_id'       => 'nullable|exists:majors,id',
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
            $teacher->user->update($userData);

            $teacherData = [
                'nip'            => $validated['nip'],
                'phone'          => $validated['phone'],
                'address'        => $validated['address'],
                'specialization' => $validated['specialization'],
                'position'       => $validated['position'] ?? ['Guru Mata Pelajaran'],
                'classroom_id'   => in_array('Wali Kelas', $validated['position'] ?? []) ? ($validated['classroom_id'] ?? null) : null,
                'major_id'       => (in_array('Kepala Program Keahlian (Kajur)', $validated['position'] ?? []) || in_array('Kepala Bengkel / Laboratorium', $validated['position'] ?? [])) ? ($validated['major_id'] ?? null) : null,
            ];

            if ($request->hasFile('photo')) {
                if ($teacher->photo && \Storage::disk('public')->exists($teacher->photo)) {
                    \Storage::disk('public')->delete($teacher->photo);
                }
                $teacherData['photo'] = $request->file('photo')->store('teachers/photos', 'public');
            }

            $teacher->update($teacherData);

            $nonTeaching = ['Kepala Sekolah', 'Wakasek Kurikulum', 'Wakasek Kesiswaan', 'Wakasek Hubin / Humas', 'Wakasek Sarana Prasarana', 'Staf Tata Usaha (TU)', 'Pustakawan', 'Kepala Bengkel / Laboratorium', 'Kepala Program Keahlian (Kajur)'];
            $positions = $validated['position'] ?? ['Guru Mata Pelajaran'];
            $hasTeachingPosition = count(array_diff($positions, $nonTeaching)) > 0;
            
            if ($hasTeachingPosition) {
                $teacher->taughtClassrooms()->sync($validated['taught_classes'] ?? []);
                $teacher->taughtSubjects()->sync($validated['taught_subjects'] ?? []);
            } else {
                $teacher->taughtClassrooms()->detach();
                $teacher->taughtSubjects()->detach();
            }

            DB::commit();
            return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            DB::beginTransaction();
            $user = $teacher->user;
            $teacher->forceDelete();
            if ($user) {
                $user->delete();
            }
            DB::commit();
            return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
