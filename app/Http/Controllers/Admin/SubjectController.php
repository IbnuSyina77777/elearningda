<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Major;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('level') && $request->level != '') {
            $query->where('level', $request->level);
        }

        $subjects = $query->with('major')->orderBy('level')->orderBy('name')->get();

        $groupedSubjects = [
            'X'   => $subjects->where('level', 'X'),
            'XI'  => $subjects->where('level', 'XI'),
            'XII' => $subjects->where('level', 'XII'),
        ];

        return view('admin.subjects.index', compact('groupedSubjects'));
    }

    public function create()
    {
        $majors = Major::active()->get();
        return view('admin.subjects.create', compact('majors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'major_id'    => 'nullable|exists:majors,id',
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:subjects,code',
            'level'       => 'required|in:X,XI,XII',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = true;

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(Subject $subject)
    {
        $majors = Major::active()->get();
        return view('admin.subjects.edit', compact('subject', 'majors'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'major_id'    => 'nullable|exists:majors,id',
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'level'       => 'required|in:X,XI,XII',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
