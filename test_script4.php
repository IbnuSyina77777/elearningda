<?php
$teacher = App\Models\Teacher::find(1);
$selectedClassroomId = 5;
$selectedSubject = App\Models\Subject::find(1);

$activeYear = App\Models\AcademicYear::where('is_active', true)->first();
$selectedYearId = $activeYear->id ?? null;

if ($teacher) {
    $gradeSetting = App\Models\AttendanceWeight::where('subject_id', $selectedSubject->id)
        ->where('classroom_id', $selectedClassroomId)
        ->where('teacher_id', $teacher->id)
        ->where('academic_year_id', $selectedYearId)
        ->first();
        
    echo "Grade Setting: " . ($gradeSetting ? 'FOUND' : 'NULL') . "\n";
    if ($gradeSetting) {
        echo "Attendance Weight: {$gradeSetting->weight}, PTS: {$gradeSetting->pts_weight}, PAS: {$gradeSetting->pas_weight}\n";
    }

    $assignments = App\Models\Assignment::where('subject_id', $selectedSubject->id)
        ->where('classroom_id', $selectedClassroomId)
        ->where('teacher_id', $teacher->id)
        ->where('academic_year_id', $selectedYearId)
        ->with('submissions')
        ->get();
    echo "Assignments: " . count($assignments) . "\n";
    
    $students = App\Models\Student::where('classroom_id', $selectedClassroomId)->get();
    echo "Students: " . count($students) . "\n";
    
    $examScores = App\Models\ExamScore::where('subject_id', $selectedSubject->id)
        ->where('classroom_id', $selectedClassroomId)
        ->where('teacher_id', $teacher->id)
        ->where('academic_year_id', $selectedYearId)
        ->get()->keyBy('student_id');

    $attendances = App\Models\Attendance::where('subject_id', $selectedSubject->id)
        ->where('classroom_id', $selectedClassroomId)
        ->where('teacher_id', $teacher->id)
        ->get();

    $totalSessions = App\Models\Attendance::where('subject_id', $selectedSubject->id)
        ->where('classroom_id', $selectedClassroomId)
        ->where('teacher_id', $teacher->id)
        ->distinct('date')
        ->count('date');

    foreach ($students as $student) {
        $weightedSum = 0;
        $attendanceWeight = $gradeSetting ? $gradeSetting->weight : 0;
        $ptsWeight = $gradeSetting ? $gradeSetting->pts_weight : 0;
        $pasWeight = $gradeSetting ? $gradeSetting->pas_weight : 0;
        
        $totalWeight = $attendanceWeight + $ptsWeight + $pasWeight;

        $studentAttendances = $attendances->where('student_id', $student->id);
        $attendanceScore = 0;
        if ($totalSessions > 0) {
            $h = $studentAttendances->where('status', 'hadir')->count();
            $s = $studentAttendances->where('status', 'sakit')->count();
            $i = $studentAttendances->where('status', 'izin')->count();
            $totalPoints = ($h * 100) + ($s * 80) + ($i * 80);
            $attendanceScore = $totalPoints / $totalSessions;
        }
        $weightedSum += ($attendanceScore * ($attendanceWeight / 100));

        $ptsScore = $examScores->has($student->id) ? (int)$examScores[$student->id]->pts_score : 0;
        $pasScore = $examScores->has($student->id) ? (int)$examScores[$student->id]->pas_score : 0;
        
        $weightedSum += ($ptsScore * ($ptsWeight / 100));
        $weightedSum += ($pasScore * ($pasWeight / 100));

        foreach ($assignments as $a) {
            $sub = $a->submissions->where('student_id', $student->id)->first();
            $grade = $sub ? (int) $sub->grade : 0;
            $weightedSum += ($grade * ($a->weight / 100));
            $totalWeight += $a->weight;
        }
        
        echo "Student " . $student->id . " - finalScore: " . round($weightedSum, 2) . " (total_weight: $totalWeight)\n";
    }
}
