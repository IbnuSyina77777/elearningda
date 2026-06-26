<?php
$classroom = App\Models\Classroom::first();
$subject = App\Models\Subject::where('level', $classroom->level)->first();
$teacher = $subject->taughtBy()->whereHas('taughtClassrooms', function($q) use ($classroom) { 
    $q->where('classrooms.id', $classroom->id); 
})->first();

echo 'Classroom: ' . $classroom->name . PHP_EOL;
echo 'Subject: ' . $subject->name . PHP_EOL;
echo 'Teacher found: ' . ($teacher ? $teacher->id : 'NO') . PHP_EOL;

$activeYear = App\Models\AcademicYear::where('is_active', true)->first();
$yearId = $activeYear->id ?? null;
echo 'Year ID: ' . $yearId . PHP_EOL;

if ($teacher) {
    $gradeSetting = App\Models\AttendanceWeight::where('subject_id', $subject->id)
        ->where('classroom_id', $classroom->id)
        ->where('teacher_id', $teacher->id)
        ->where('academic_year_id', $yearId)
        ->first();
    echo 'Grade Setting found: ' . ($gradeSetting ? 'YES' : 'NO') . PHP_EOL;

    $assignments = App\Models\Assignment::where('subject_id', $subject->id)
        ->where('classroom_id', $classroom->id)
        ->where('teacher_id', $teacher->id)
        ->where('academic_year_id', $yearId)
        ->count();
    echo 'Assignments count: ' . $assignments . PHP_EOL;
}
