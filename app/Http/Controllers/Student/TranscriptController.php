<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\AttendanceWeight;
use App\Models\ExamScore;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class TranscriptController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user()->student;
        if (!$student) {
            return redirect('/')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->query('academic_year_id', $activeYear->id ?? ($academicYears->first()->id ?? null));
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId);

        // Determine Historical Classroom (to support Alumni and Promoted Students viewing past grades)
        $historicalClassroomId = ExamScore::where('student_id', $student->id)
            ->where('academic_year_id', $selectedYearId)
            ->value('classroom_id');

        $classroomId = $historicalClassroomId ?: $student->classroom_id;
        $classroom = \App\Models\Classroom::find($classroomId);

        if (!$classroom) {
            $subjects = collect();
        } else {
            $subjects = Subject::where('level', $classroom->level)->get();
        }

        $transcripts = collect();

        foreach ($subjects as $subject) {
            // Check if teacher is assigned to this classroom for this subject
            $teacher = $subject->taughtBy()->whereHas('taughtClassrooms', function($q) use ($classroomId) {
                $q->where('classrooms.id', $classroomId);
            })->first();

            if (!$teacher) {
                continue;
            }

            // 1. Get Attendance Weight & Exam Weights
            $gradeSetting = AttendanceWeight::where('subject_id', $subject->id)
                ->where('classroom_id', $classroomId)
                ->where('teacher_id', $teacher->id)
                ->where('academic_year_id', $selectedYearId)
                ->first();
            
            $attendanceWeight = $gradeSetting ? $gradeSetting->weight : 0;
            $ptsWeight = $gradeSetting ? $gradeSetting->pts_weight : 0;
            $pasWeight = $gradeSetting ? $gradeSetting->pas_weight : 0;

            // 2. Get Total Sessions for this subject, classroom, teacher
            $totalSessions = Attendance::where('subject_id', $subject->id)
                ->where('classroom_id', $classroomId)
                ->where('teacher_id', $teacher->id)
                ->distinct('date')
                ->count('date');

            // 3. Get Student's Attendances
            $attendances = Attendance::where('subject_id', $subject->id)
                ->where('classroom_id', $classroomId)
                ->where('teacher_id', $teacher->id)
                ->where('student_id', $student->id)
                ->get();

            $attendanceScore = 0;
            if ($totalSessions > 0) {
                $h = $attendances->where('status', 'hadir')->count();
                $s = $attendances->where('status', 'sakit')->count();
                $i = $attendances->where('status', 'izin')->count();
                
                $totalPoints = ($h * 100) + ($s * 80) + ($i * 80);
                $attendanceScore = $totalPoints / $totalSessions;
            }

            // 4. Get Exam Scores
            $exam = ExamScore::where('subject_id', $subject->id)
                ->where('classroom_id', $classroomId)
                ->where('teacher_id', $teacher->id)
                ->where('student_id', $student->id)
                ->where('academic_year_id', $selectedYearId)
                ->first();
            
            $ptsScore = $exam ? (int) $exam->pts_score : 0;
            $pasScore = $exam ? (int) $exam->pas_score : 0;

            // 5. Calculate Assignments Score
            $assignments = Assignment::where('subject_id', $subject->id)
                ->where('classroom_id', $classroomId)
                ->where('teacher_id', $teacher->id)
                ->where('academic_year_id', $selectedYearId)
                ->with(['submissions' => function($q) use ($student) {
                    $q->where('student_id', $student->id);
                }])
                ->get();

            $assignmentsSum = 0;
            $assignmentsTotalWeight = 0;
            $assignmentsScores = [];

            foreach ($assignments as $a) {
                $sub = $a->submissions->first();
                $grade = $sub && !is_null($sub->grade) ? (int) $sub->grade : 0;
                
                $assignmentsSum += ($grade * ($a->weight / 100));
                $assignmentsTotalWeight += $a->weight;

                $assignmentsScores[] = [
                    'title' => $a->title,
                    'grade' => $grade,
                    'weight' => $a->weight
                ];
            }

            // 6. Final Grade
            $finalScore = ($attendanceScore * ($attendanceWeight / 100)) + 
                          ($ptsScore * ($ptsWeight / 100)) + 
                          ($pasScore * ($pasWeight / 100)) + 
                          $assignmentsSum;
                          
            $totalWeight = $attendanceWeight + $ptsWeight + $pasWeight + $assignmentsTotalWeight;

            // Determine predicate based on final score
            if ($finalScore >= 90) {
                $predicate = 'A';
            } elseif ($finalScore >= 80) {
                $predicate = 'B';
            } elseif ($finalScore >= 70) {
                $predicate = 'C';
            } elseif ($finalScore >= 60) {
                $predicate = 'D';
            } else {
                $predicate = 'E';
            }

            $transcripts->put($subject->id, [
                'subject_name' => $subject->name,
                'subject_code' => $subject->code,
                'teacher_name' => $teacher->name ?? $teacher->user->name ?? 'Guru',
                'attendance_score' => round($attendanceScore, 1),
                'attendance_weight' => $attendanceWeight,
                'pts_score' => $ptsScore,
                'pts_weight' => $ptsWeight,
                'pas_score' => $pasScore,
                'pas_weight' => $pasWeight,
                'assignments_scores' => $assignmentsScores,
                'assignments_sum' => round($assignmentsSum),
                'assignments_total_weight' => $assignmentsTotalWeight,
                'final_score' => round($finalScore),
                'total_weight' => $totalWeight,
                'predicate' => $predicate,
            ]);
        }

        // Sort by subject name
        $transcripts = $transcripts->sortBy('subject_name');

        return view('student.transcript.index', compact('transcripts', 'academicYears', 'selectedYearId', 'selectedYear'));
    }
}
