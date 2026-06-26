<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\AttendanceWeight;
use App\Models\ExamScore;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request, Subject $subject)
    {
        $this->authorizeTeacher($subject);
        $teacher = auth()->user()->teacher;

        $classrooms = $teacher->taughtClassrooms()->where('level', $subject->level)->get();
        if ($classrooms->isEmpty()) {
            return back()->with('error', 'Anda belum ditugaskan mengajar kelas manapun untuk jenjang ini.');
        }

        $selectedClassroomId = $request->query('classroom_id', $classrooms->first()->id);
        $selectedClassroom = $classrooms->firstWhere('id', $selectedClassroomId);
        if (!$selectedClassroom) abort(404, 'Kelas tidak ditemukan.');

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->query('academic_year_id', $activeYear->id ?? ($academicYears->first()->id ?? null));
        
        $students = Student::where('classroom_id', $selectedClassroomId)->with('user')->get()->sortBy('name');

        $gradeSetting = AttendanceWeight::firstOrCreate(
            [
                'subject_id' => $subject->id,
                'classroom_id' => $selectedClassroomId,
                'teacher_id' => $teacher->id,
                'academic_year_id' => $selectedYearId
            ],
            [
                'weight' => 0,
                'pts_weight' => 0,
                'pas_weight' => 0
            ]
        );
        $ptsWeight = $gradeSetting->pts_weight;
        $pasWeight = $gradeSetting->pas_weight;

        $examScores = ExamScore::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->where('academic_year_id', $selectedYearId)
            ->get()->keyBy('student_id');

        return view('teacher.exams.index', compact('subject', 'classrooms', 'selectedClassroom', 'academicYears', 'selectedYearId', 'students', 'ptsWeight', 'pasWeight', 'examScores'));
    }

    public function store(Request $request, Subject $subject)
    {
        $this->authorizeTeacher($subject);
        
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'pts_weight' => 'required|numeric|min:0|max:100',
            'pas_weight' => 'required|numeric|min:0|max:100',
            'pts' => 'nullable|array',
            'pts.*' => 'nullable|numeric|min:0|max:100',
            'pas' => 'nullable|array',
            'pas.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $teacher = auth()->user()->teacher;

        $gradeSetting = AttendanceWeight::firstOrCreate(
            [
                'subject_id' => $subject->id,
                'classroom_id' => $request->classroom_id,
                'teacher_id' => $teacher->id,
                'academic_year_id' => $request->academic_year_id
            ],
            ['weight' => 0, 'pts_weight' => 0, 'pas_weight' => 0]
        );
        
        $gradeSetting->update([
            'pts_weight' => $request->pts_weight,
            'pas_weight' => $request->pas_weight,
        ]);

        $students = Student::where('classroom_id', $request->classroom_id)->pluck('id');
        foreach ($students as $studentId) {
            $pts = isset($request->pts[$studentId]) ? $request->pts[$studentId] : null;
            $pas = isset($request->pas[$studentId]) ? $request->pas[$studentId] : null;

            if ($pts !== null || $pas !== null) {
                ExamScore::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'classroom_id' => $request->classroom_id,
                        'teacher_id' => $teacher->id,
                        'student_id' => $studentId,
                        'academic_year_id' => $request->academic_year_id
                    ],
                    [
                        'pts_score' => $pts,
                        'pas_score' => $pas,
                    ]
                );
            }
        }

        return redirect()->route('teacher.exams.index', ['subject' => $subject->id, 'classroom_id' => $request->classroom_id, 'academic_year_id' => $request->academic_year_id])
            ->with('success', 'Bobot dan Nilai Ujian (PTS & PAS) berhasil diperbarui.');
    }

    private function authorizeTeacher(Subject $subject)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher || !$teacher->taughtSubjects->contains($subject->id)) {
            abort(403, 'Akses ditolak.');
        }
    }
}
