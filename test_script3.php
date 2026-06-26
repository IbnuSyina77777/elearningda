<?php
$classroomId = App\Models\Classroom::first()->id;
$subjectId = App\Models\Subject::where('level', App\Models\Classroom::find($classroomId)->level)->first()->id;

$selectedClassroomId = $classroomId;
$selectedSubjectId = $subjectId;
$selectedSubject = App\Models\Subject::find($selectedSubjectId);

$teacher = $selectedSubject->taughtBy()->whereHas('taughtClassrooms', function($q) use ($selectedClassroomId) {
    $q->where('classrooms.id', $selectedClassroomId);
})->first();

$activeYear = App\Models\AcademicYear::where('is_active', true)->first();
$selectedYearId = $activeYear->id ?? null;

echo "Teacher ID: " . ($teacher ? $teacher->id : 'NULL') . "\n";
echo "Classroom ID: $selectedClassroomId\n";
echo "Subject ID: $selectedSubjectId\n";
echo "Year ID: $selectedYearId\n";

if ($teacher) {
    $gradeSetting = App\Models\AttendanceWeight::where('subject_id', $selectedSubject->id)
        ->where('classroom_id', $selectedClassroomId)
        ->where('teacher_id', $teacher->id)
        ->where('academic_year_id', $selectedYearId)
        ->first();
        
    echo "Grade Setting: " . json_encode($gradeSetting) . "\n";

    $assignments = App\Models\Assignment::where('subject_id', $selectedSubject->id)
        ->where('classroom_id', $selectedClassroomId)
        ->where('teacher_id', $teacher->id)
        ->where('academic_year_id', $selectedYearId)
        ->with('submissions')
        ->get();
    echo "Assignments: " . count($assignments) . "\n";
    
    $students = App\Models\Student::where('classroom_id', $selectedClassroomId)->get();
    echo "Students: " . count($students) . "\n";
    
    foreach ($students as $student) {
        $weightedSum = 0;
        $attendanceWeight = $gradeSetting ? $gradeSetting->weight : 0;
        $ptsWeight = $gradeSetting ? $gradeSetting->pts_weight : 0;
        $pasWeight = $gradeSetting ? $gradeSetting->pas_weight : 0;
        
        $totalWeight = $attendanceWeight + $ptsWeight + $pasWeight;
        foreach ($assignments as $a) {
            $sub = $a->submissions->where('student_id', $student->id)->first();
            $grade = $sub ? (int) $sub->grade : 0;
            $weightedSum += ($grade * ($a->weight / 100));
            $totalWeight += $a->weight;
        }
        
        echo "Student " . $student->id . " score: " . $weightedSum . " total_weight: " . $totalWeight . "\n";
    }
}
