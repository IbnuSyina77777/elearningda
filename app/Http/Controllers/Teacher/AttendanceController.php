<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
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
        
        $date = $request->query('date', now()->format('Y-m-d'));

        if (!$selectedClassroom) {
            abort(404, 'Kelas tidak ditemukan.');
        }

        $students = Student::where('classroom_id', $selectedClassroomId)->with('user')->get()->sortBy('name');
        
        // Get attendance for the selected date
        $attendances = Attendance::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        // Get past dates
        $pastDates = Attendance::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->select('date')
            ->distinct()
            ->orderByDesc('date')
            ->pluck('date');

        return view('teacher.attendances.index', compact('subject', 'classrooms', 'selectedClassroom', 'students', 'date', 'attendances', 'pastDates'));
    }

    public function store(Request $request, Subject $subject)
    {
        $this->authorizeTeacher($subject);
        $teacher = auth()->user()->teacher;

        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'date' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'in:hadir,sakit,izin,alpa'
        ]);

        $classroomId = $request->classroom_id;
        $date = $request->date;

        foreach ($request->status as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'classroom_id' => $classroomId,
                    'teacher_id' => $teacher->id,
                    'student_id' => $studentId,
                    'date' => $date
                ],
                [
                    'status' => $status
                ]
            );
        }

        return redirect()->route('teacher.attendances.index', ['subject' => $subject->id, 'classroom_id' => $classroomId, 'date' => $date])
            ->with('success', 'Absensi berhasil disimpan.');
    }

    private function authorizeTeacher(Subject $subject)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher || !$teacher->taughtSubjects->contains($subject->id)) {
            abort(403, 'Akses ditolak.');
        }
    }
}
