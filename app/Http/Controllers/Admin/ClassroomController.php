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
}
