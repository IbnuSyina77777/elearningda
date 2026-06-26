<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\AttendanceWeight;
use App\Models\ExamScore;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class GradeController extends Controller
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

        // Get assignments for this subject & classroom & academic year
        $assignments = Assignment::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->where('academic_year_id', $selectedYearId)
            ->with('submissions') 
            ->orderBy('created_at')
            ->get();

        // Get Attendance & Exam Weights
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
        $attendanceWeight = $gradeSetting->weight;
        $ptsWeight = $gradeSetting->pts_weight;
        $pasWeight = $gradeSetting->pas_weight;

        // Get Total Attendance Sessions
        $totalSessions = Attendance::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->distinct('date')
            ->count('date'); // We could filter attendance by date range of academic year if needed, but assuming one class per year is enough for now.

        $students = Student::where('classroom_id', $selectedClassroomId)->with('user')->get()->sortBy('name');

        $attendancesByStudent = Attendance::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->get()
            ->groupBy('student_id');

        $subsByAssignment = [];
        foreach ($assignments as $a) {
            $subsByAssignment[$a->id] = $a->submissions->keyBy('student_id');
        }

        $examScores = ExamScore::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->where('academic_year_id', $selectedYearId)
            ->get()->keyBy('student_id');

        // Calculate final grades
        $finalGrades = [];
        foreach ($students as $student) {
            $weightedSum = 0;
            $totalWeight = $attendanceWeight + $ptsWeight + $pasWeight; 
            
            // Calculate Attendance Score
            $studentAttendances = $attendancesByStudent->get($student->id, collect());
            $attendanceScore = 0;
            
            if ($totalSessions > 0) {
                // Rule: Hadir=100, Sakit=80, Izin=80, Alpa=0
                $statusCounts = $studentAttendances->countBy('status');
                $h = $statusCounts->get('hadir', 0);
                $s = $statusCounts->get('sakit', 0);
                $i = $statusCounts->get('izin', 0);
                
                $totalPoints = ($h * 100) + ($s * 80) + ($i * 80);
                $attendanceScore = $totalPoints / $totalSessions;
            }
            
            $weightedSum += ($attendanceScore * ($attendanceWeight / 100));

            // Exam Scores
            $ptsScore = $examScores->has($student->id) ? (int)$examScores[$student->id]->pts_score : 0;
            $pasScore = $examScores->has($student->id) ? (int)$examScores[$student->id]->pas_score : 0;
            
            $weightedSum += ($ptsScore * ($ptsWeight / 100));
            $weightedSum += ($pasScore * ($pasWeight / 100));

            // Calculate Assignments Score
            foreach ($assignments as $a) {
                $sub = $subsByAssignment[$a->id]->get($student->id);
                $grade = $sub ? (int) $sub->grade : 0;
                
                $weightedSum += ($grade * ($a->weight / 100));
                $totalWeight += $a->weight;
            }
            
            $finalGrades[$student->id] = [
                'attendance_score' => round($attendanceScore, 1),
                'pts_score' => $ptsScore,
                'pas_score' => $pasScore,
                'score' => round($weightedSum),
                'total_weight' => $totalWeight,
            ];
        }

        return view('teacher.grades.index', compact('subject', 'classrooms', 'selectedClassroom', 'academicYears', 'selectedYearId', 'assignments', 'attendanceWeight', 'ptsWeight', 'pasWeight', 'totalSessions', 'students', 'finalGrades'));
    }

    public function export(Request $request, Subject $subject)
    {
        $this->authorizeTeacher($subject);
        $teacher = auth()->user()->teacher;

        $classrooms = $teacher->taughtClassrooms()->where('level', $subject->level)->get();
        $selectedClassroomId = $request->query('classroom_id', $classrooms->first()->id ?? null);
        $selectedClassroom = $classrooms->firstWhere('id', $selectedClassroomId);

        if (!$selectedClassroom) {
            return back()->with('error', 'Kelas tidak ditemukan.');
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->query('academic_year_id', $activeYear->id ?? null);

        $assignments = Assignment::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->where('academic_year_id', $selectedYearId)
            ->with('submissions') 
            ->orderBy('created_at')
            ->get();

        $gradeSetting = AttendanceWeight::firstOrCreate(
            [
                'subject_id' => $subject->id,
                'classroom_id' => $selectedClassroomId,
                'teacher_id' => $teacher->id,
                'academic_year_id' => $selectedYearId
            ],
            ['weight' => 0, 'pts_weight' => 0, 'pas_weight' => 0]
        );
        $attendanceWeight = $gradeSetting->weight;
        $ptsWeight = $gradeSetting->pts_weight;
        $pasWeight = $gradeSetting->pas_weight;

        $totalSessions = Attendance::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->distinct('date')
            ->count('date');

        $students = Student::where('classroom_id', $selectedClassroomId)->with('user')->get()->sortBy('name');

        $attendancesByStudent = Attendance::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->get()
            ->groupBy('student_id');

        $subsByAssignment = [];
        foreach ($assignments as $a) {
            $subsByAssignment[$a->id] = $a->submissions->keyBy('student_id');
        }

        $examScores = ExamScore::where('subject_id', $subject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('teacher_id', $teacher->id)
            ->where('academic_year_id', $selectedYearId)
            ->get()->keyBy('student_id');

        $finalGrades = [];
        foreach ($students as $student) {
            $weightedSum = 0;
            
            $studentAttendances = $attendancesByStudent->get($student->id, collect());
            $attendanceScore = 0;
            if ($totalSessions > 0) {
                $statusCounts = $studentAttendances->countBy('status');
                $h = $statusCounts->get('hadir', 0);
                $s = $statusCounts->get('sakit', 0);
                $i = $statusCounts->get('izin', 0);
                $attendanceScore = (($h * 100) + ($s * 80) + ($i * 80)) / $totalSessions;
            }
            $weightedSum += ($attendanceScore * ($attendanceWeight / 100));

            $ptsScore = $examScores->has($student->id) ? (int)$examScores[$student->id]->pts_score : 0;
            $pasScore = $examScores->has($student->id) ? (int)$examScores[$student->id]->pas_score : 0;
            
            $weightedSum += ($ptsScore * ($ptsWeight / 100));
            $weightedSum += ($pasScore * ($pasWeight / 100));

            foreach ($assignments as $a) {
                $sub = $subsByAssignment[$a->id]->get($student->id);
                $grade = $sub ? (int) $sub->grade : 0;
                $weightedSum += ($grade * ($a->weight / 100));
            }
            
            $finalGrades[$student->id] = [
                'attendance_score' => round($attendanceScore, 1),
                'pts_score' => $ptsScore,
                'pas_score' => $pasScore,
                'score' => round($weightedSum),
            ];
        }

        $fileName = 'Rekap_Nilai_' . preg_replace('/[^A-Za-z0-9_-]/', '', $subject->name) . '_' . preg_replace('/[^A-Za-z0-9_-]/', '', $selectedClassroom->name) . '_' . date('Ymd') . '.xlsx';

        $columns = ['NIS', 'Nama Siswa', 'L/P', 'Nilai Kehadiran', 'PTS', 'PAS'];
        foreach ($assignments as $a) {
            $columns[] = $a->title;
        }
        $columns[] = 'Nilai Akhir';
        $columns[] = 'Predikat';

        $data = [];
        foreach ($students as $student) {
            $row = [
                $student->nis,
                $student->name,
                $student->gender,
                $finalGrades[$student->id]['attendance_score'] ?? 0,
                $finalGrades[$student->id]['pts_score'] ?? 0,
                $finalGrades[$student->id]['pas_score'] ?? 0,
            ];
            foreach ($assignments as $a) {
                $sub = $subsByAssignment[$a->id]->get($student->id);
                $row[] = $sub ? (int) $sub->grade : 0;
            }
            $finalScore = $finalGrades[$student->id]['score'] ?? 0;
            $row[] = $finalScore;
            $row[] = $finalScore >= 90 ? 'A' : ($finalScore >= 75 ? 'B' : ($finalScore >= 60 ? 'C' : 'D'));
            $data[] = $row;
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\GradesExport($columns, $data), $fileName);
    }

    public function storeGrades(Request $request, Subject $subject)
    {
        $this->authorizeTeacher($subject);
        
        $request->validate([
            'weights' => 'nullable|array',
            'weights.*' => 'numeric|min:0|max:100',
            'attendance_weight' => 'required|numeric|min:0|max:100',
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
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
        $gradeSetting->update(['weight' => $request->attendance_weight]);

        $totalWeight = $request->attendance_weight + $gradeSetting->pts_weight + $gradeSetting->pas_weight;

        if ($request->has('weights')) {
            foreach ($request->weights as $assignmentId => $weight) {
                $assignment = Assignment::where('id', $assignmentId)
                    ->where('subject_id', $subject->id)
                    ->where('teacher_id', $teacher->id)
                    ->first();
                
                if ($assignment) {
                    $assignment->update(['weight' => $weight]);
                    $totalWeight += $weight;
                }
            }
        }

        if ($totalWeight > 100) {
            return redirect()->route('teacher.grades.index', ['subject' => $subject->id, 'classroom_id' => $request->classroom_id, 'academic_year_id' => $request->academic_year_id])
                ->with('warning', 'Data berhasil disimpan, namun total bobot melebihi 100% (' . $totalWeight . '%). Harap periksa kembali.');
        }

        return redirect()->route('teacher.grades.index', ['subject' => $subject->id, 'classroom_id' => $request->classroom_id, 'academic_year_id' => $request->academic_year_id])
            ->with('success', 'Bobot berhasil diperbarui. Total bobot: ' . $totalWeight . '%');
    }

    private function authorizeTeacher(Subject $subject)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher || !$teacher->taughtSubjects->contains($subject->id)) {
            abort(403, 'Akses ditolak.');
        }
    }
}
