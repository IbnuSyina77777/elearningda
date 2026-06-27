<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Major;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::with('major', 'homeroomTeacher.user')->withCount('students')->get();
        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        $majors = Major::all();
        return view('admin.classrooms.create', compact('majors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'major_id'  => 'required|exists:majors,id',
            'name'      => 'required|string|max:255',
            'level'     => 'required|in:X,XI,XII',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Classroom::create($validated);

        return redirect()->route('admin.classrooms.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Classroom $classroom)
    {
        $majors = Major::all();
        return view('admin.classrooms.edit', compact('classroom', 'majors'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'major_id'  => 'required|exists:majors,id',
            'name'      => 'required|string|max:255',
            'level'     => 'required|in:X,XI,XII',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $classroom->update($validated);

        return redirect()->route('admin.classrooms.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        if ($classroom->students()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa.');
        }

        $classroom->delete();
        return redirect()->route('admin.classrooms.index')->with('success', 'Data kelas berhasil dihapus.');
    }

    public function promotion(Classroom $classroom)
    {
        $classroom->loadCount('students');
        // Get other classrooms to promote to
        $classrooms = Classroom::where('id', '!=', $classroom->id)
            ->where('is_active', true)
            ->with('major')
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        return view('admin.classrooms.promotion', compact('classroom', 'classrooms'));
    }

    public function promote(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'action' => 'required|in:promote,graduate',
            'target_classroom_id' => 'required_if:action,promote|nullable|exists:classrooms,id',
        ]);

        $students = $classroom->students;

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa di kelas ini untuk diproses.');
        }

        if ($validated['action'] === 'graduate') {
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            $graduationYear = $activeYear ? $activeYear->name : date('Y');
            
            foreach ($students as $student) {
                $student->update([
                    'classroom_id' => null,
                    'status' => 'alumni',
                    'graduated_from' => $classroom->name,
                    'graduation_year' => $graduationYear
                ]);
            }
            return redirect()->route('admin.classrooms.index')->with('success', $students->count() . ' siswa berhasil ditandai sebagai Lulus (Alumni).');
        } else {
            $targetClassroom = Classroom::find($validated['target_classroom_id']);
            foreach ($students as $student) {
                $student->update([
                    'classroom_id' => $targetClassroom->id,
                    'status' => 'active'
                ]);
            }
            return redirect()->route('admin.classrooms.index')->with('success', $students->count() . " siswa berhasil dipindahkan ke kelas {$targetClassroom->name}.");
        }
    }
}
