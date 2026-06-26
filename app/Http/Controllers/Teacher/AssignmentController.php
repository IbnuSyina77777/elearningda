<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request, Subject $subject)
    {
        $this->authorizeTeacher($subject);
        $teacher = auth()->user()->teacher;

        $academicYears = \App\Models\AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->query('academic_year_id', $activeYear->id ?? ($academicYears->first()->id ?? null));

        $assignments = Assignment::where('teacher_id', $teacher->id)
                                 ->where('subject_id', $subject->id)
                                 ->where('academic_year_id', $selectedYearId)
                                 ->with(['classroom'])
                                 ->withCount('submissions')
                                 ->orderBy('due_date', 'desc')
                                 ->get();
        return view('teacher.assignments.index', compact('subject', 'assignments', 'academicYears', 'selectedYearId'));
    }

    public function create(Subject $subject)
    {
        $this->authorizeTeacher($subject);
        $classrooms = auth()->user()->teacher->taughtClassrooms()->where('level', $subject->level)->get();
        return view('teacher.assignments.create', compact('subject', 'classrooms'));
    }

    public function store(Request $request, Subject $subject)
    {
        $this->authorizeTeacher($subject);
        $teacher = auth()->user()->teacher;

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'classroom_id'   => 'required|array|min:1',
            'classroom_id.*' => 'exists:classrooms,id',
            'description'    => 'nullable|string',
            'file'           => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip|max:10240',
            'due_date'       => 'required|date|after:now',
        ]);

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('assignments', 'public');
            $fileName = $file->getClientOriginalName();
        }

        foreach ($validated['classroom_id'] as $cid) {
            Assignment::create([
                'subject_id'       => $subject->id,
                'teacher_id'       => $teacher->id,
                'classroom_id'     => $cid,
                'academic_year_id' => $activeYear->id,
                'title'            => $validated['title'],
                'description'      => $validated['description'],
                'due_date'         => $validated['due_date'],
                'file_path'        => $filePath,
                'file_name'        => $fileName,
            ]);
        }

        return redirect()->route('teacher.assignments.index', $subject->id)->with('success', 'Tugas berhasil dibuat untuk kelas yang dipilih.');
    }

    public function destroy(Subject $subject, Assignment $assignment)
    {
        $this->authorizeTeacher($subject);

        if ($assignment->file_path && \Storage::disk('public')->exists($assignment->file_path)) {
            \Storage::disk('public')->delete($assignment->file_path);
        }

        $assignment->delete();
        return redirect()->route('teacher.assignments.index', $subject->id)->with('success', 'Tugas berhasil dihapus.');
    }

    public function submissions(Subject $subject, Assignment $assignment)
    {
        $this->authorizeTeacher($subject);
        $submissions = $assignment->submissions()->with('student.user')->get();
        return view('teacher.assignments.submissions', compact('subject', 'assignment', 'submissions'));
    }

    public function grade(Request $request, Subject $subject, Assignment $assignment, \App\Models\Submission $submission)
    {
        $this->authorizeTeacher($subject);

        $validated = $request->validate([
            'grade'    => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade'     => $validated['grade'],
            'feedback'  => $validated['feedback'],
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Nilai berhasil disimpan untuk ' . ($submission->student->name ?? 'siswa') . '.');
    }

    private function authorizeTeacher(Subject $subject)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher || !$teacher->taughtSubjects->contains($subject->id)) {
            abort(403, 'Akses ditolak.');
        }
    }
}
