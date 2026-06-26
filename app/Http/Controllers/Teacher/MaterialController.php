<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request, Subject $subject)
    {
        $this->authorizeTeacher($subject);
        $teacher = auth()->user()->teacher;

        $academicYears = \App\Models\AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->query('academic_year_id', $activeYear->id ?? ($academicYears->first()->id ?? null));

        $materials = Material::where('teacher_id', $teacher->id)
                             ->where('subject_id', $subject->id)
                             ->where('academic_year_id', $selectedYearId)
                             ->with('classroom')
                             ->orderBy('order')
                             ->get();
        return view('teacher.materials.index', compact('subject', 'materials', 'academicYears', 'selectedYearId'));
    }

    public function create(Subject $subject)
    {
        $this->authorizeTeacher($subject);
        $classrooms = auth()->user()->teacher->taughtClassrooms()->where('level', $subject->level)->get();
        return view('teacher.materials.create', compact('subject', 'classrooms'));
    }

    public function store(Request $request, Subject $subject)
    {
        $this->authorizeTeacher($subject);
        $teacher = auth()->user()->teacher;

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'classroom_id'   => 'required|array|min:1',
            'classroom_id.*' => 'exists:classrooms,id',
            'content'        => 'nullable|string',
            'file'           => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip|max:10240',
            'order'          => 'nullable|integer',
        ]);

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('materials', 'public');
            $fileName = $file->getClientOriginalName();
        }

        foreach ($validated['classroom_id'] as $cid) {
            Material::create([
                'subject_id'       => $subject->id,
                'teacher_id'       => $teacher->id,
                'classroom_id'     => $cid,
                'academic_year_id' => $activeYear->id,
                'title'            => $validated['title'],
                'content'          => $validated['content'],
                'order'            => $validated['order'] ?? 0,
                'file_path'        => $filePath,
                'file_name'        => $fileName,
            ]);
        }

        return redirect()->route('teacher.materials.index', $subject->id)->with('success', 'Materi berhasil ditambahkan ke kelas yang dipilih.');
    }

    public function edit(Subject $subject, Material $material)
    {
        $this->authorizeTeacher($subject);
        $classrooms = auth()->user()->teacher->taughtClassrooms()->where('level', $subject->level)->get();
        return view('teacher.materials.edit', compact('subject', 'material', 'classrooms'));
    }

    public function update(Request $request, Subject $subject, Material $material)
    {
        $this->authorizeTeacher($subject);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'content'      => 'nullable|string',
            'file'         => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip|max:10240',
            'order'        => 'nullable|integer',
        ]);

        $data = [
            'title'        => $validated['title'],
            'classroom_id' => $validated['classroom_id'],
            'content'      => $validated['content'],
            'order'        => $validated['order'] ?? $material->order,
        ];

        if ($request->hasFile('file')) {
            if ($material->file_path && \Storage::disk('public')->exists($material->file_path)) {
                \Storage::disk('public')->delete($material->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('materials', 'public');
            $data['file_name'] = $file->getClientOriginalName();
        }

        $material->update($data);

        return redirect()->route('teacher.materials.index', $subject->id)->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Subject $subject, Material $material)
    {
        $this->authorizeTeacher($subject);

        if ($material->file_path && \Storage::disk('public')->exists($material->file_path)) {
            \Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();
        return redirect()->route('teacher.materials.index', $subject->id)->with('success', 'Materi berhasil dihapus.');
    }

    private function authorizeTeacher(Subject $subject)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher || !$teacher->taughtSubjects->contains($subject->id)) {
            abort(403, 'Akses ditolak.');
        }
    }
}
