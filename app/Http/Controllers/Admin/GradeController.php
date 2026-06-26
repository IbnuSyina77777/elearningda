<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('level')->orderBy('name')->get();
        
        $selectedClassroomId = $request->query('classroom_id', $classrooms->first()->id ?? null);
        $selectedClassroom = $classrooms->firstWhere('id', $selectedClassroomId);

        $subjects = collect();
        $selectedSubject = null;
        if ($selectedClassroom) {
            $subjects = Subject::where('level', $selectedClassroom->level)->get();
        }
        $selectedSubjectId = $request->query('subject_id', $subjects->first()->id ?? null);
        $selectedSubject = $subjects->firstWhere('id', $selectedSubjectId);

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->query('academic_year_id', $activeYear->id ?? ($academicYears->first()->id ?? null));

        $students = collect();
        $finalGrades = [];
        $assignments = collect();
        $attendanceWeight = 0;
        $ptsWeight = 0;
        $pasWeight = 0;
        $totalSessions = 0;

        if ($selectedClassroom && $selectedSubject) {
            $students = Student::where('classroom_id', $selectedClassroomId)->with('user')->get()->sortBy('name');

            $assignments = Assignment::where('subject_id', $selectedSubject->id)
                ->where('classroom_id', $selectedClassroomId)
                ->where('academic_year_id', $selectedYearId)
                ->with('submissions') 
                ->orderBy('created_at')
                ->get();

            $gradeSetting = AttendanceWeight::where('subject_id', $selectedSubject->id)
                ->where('classroom_id', $selectedClassroomId)
                ->where('academic_year_id', $selectedYearId)
                ->first();
                
            $attendanceWeight = $gradeSetting ? $gradeSetting->weight : 0;
            $ptsWeight = $gradeSetting ? $gradeSetting->pts_weight : 0;
            $pasWeight = $gradeSetting ? $gradeSetting->pas_weight : 0;

            $totalSessions = Attendance::where('subject_id', $selectedSubject->id)
                ->where('classroom_id', $selectedClassroomId)
                ->distinct('date')
                ->count('date');

            $attendancesByStudent = Attendance::where('subject_id', $selectedSubject->id)
                ->where('classroom_id', $selectedClassroomId)
                ->get()
                ->groupBy('student_id');

            $subsByAssignment = [];
            foreach ($assignments as $a) {
                $subsByAssignment[$a->id] = $a->submissions->keyBy('student_id');
            }

            $examScores = ExamScore::where('subject_id', $selectedSubject->id)
                ->where('classroom_id', $selectedClassroomId)
                ->where('academic_year_id', $selectedYearId)
                ->get()->keyBy('student_id');

            foreach ($students as $student) {
                    $weightedSum = 0;
                    $totalWeight = $attendanceWeight + $ptsWeight + $pasWeight; 
                    
                    $studentAttendances = $attendancesByStudent->get($student->id, collect());
                    $attendanceScore = 0;
                    
                    if ($totalSessions > 0) {
                        $statusCounts = $studentAttendances->countBy('status');
                        $h = $statusCounts->get('hadir', 0);
                        $s = $statusCounts->get('sakit', 0);
                        $i = $statusCounts->get('izin', 0);
                        
                        $totalPoints = ($h * 100) + ($s * 80) + ($i * 80);
                        $attendanceScore = $totalPoints / $totalSessions;
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
        }

        return view('admin.grades.index', compact('classrooms', 'selectedClassroom', 'subjects', 'selectedSubject', 'academicYears', 'selectedYearId', 'assignments', 'attendanceWeight', 'ptsWeight', 'pasWeight', 'totalSessions', 'students', 'finalGrades'));
    }

    public function export(Request $request)
    {
        $classrooms = Classroom::orderBy('level')->orderBy('name')->get();
        $selectedClassroomId = $request->query('classroom_id', $classrooms->first()->id ?? null);
        $selectedClassroom = $classrooms->firstWhere('id', $selectedClassroomId);

        $subjects = collect();
        if ($selectedClassroom) {
            $subjects = Subject::where('level', $selectedClassroom->level)->get();
        }
        $selectedSubjectId = $request->query('subject_id', $subjects->first()->id ?? null);
        $selectedSubject = $subjects->firstWhere('id', $selectedSubjectId);

        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->query('academic_year_id', $activeYear->id ?? null);

        if (!$selectedClassroom || !$selectedSubject) {
            return back()->with('error', 'Data tidak ditemukan untuk di-export.');
        }

        $students = Student::where('classroom_id', $selectedClassroomId)->with('user')->get()->sortBy('name');

        $assignments = Assignment::where('subject_id', $selectedSubject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('academic_year_id', $selectedYearId)
            ->with('submissions') 
            ->orderBy('created_at')
            ->get();

        $gradeSetting = AttendanceWeight::where('subject_id', $selectedSubject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->where('academic_year_id', $selectedYearId)
            ->first();
            
        $attendanceWeight = $gradeSetting ? $gradeSetting->weight : 0;
        $ptsWeight = $gradeSetting ? $gradeSetting->pts_weight : 0;
        $pasWeight = $gradeSetting ? $gradeSetting->pas_weight : 0;

        $totalSessions = Attendance::where('subject_id', $selectedSubject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->distinct('date')
            ->count('date');

        $attendancesByStudent = Attendance::where('subject_id', $selectedSubject->id)
            ->where('classroom_id', $selectedClassroomId)
            ->get()
            ->groupBy('student_id');

        $subsByAssignment = [];
        foreach ($assignments as $a) {
            $subsByAssignment[$a->id] = $a->submissions->keyBy('student_id');
        }

        $examScores = ExamScore::where('subject_id', $selectedSubject->id)
            ->where('classroom_id', $selectedClassroomId)
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

        $fileName = 'Rekap_Nilai_' . preg_replace('/[^A-Za-z0-9_-]/', '', $selectedSubject->name) . '_' . preg_replace('/[^A-Za-z0-9_-]/', '', $selectedClassroom->name) . '_' . date('Ymd') . '.xlsx';

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
}
