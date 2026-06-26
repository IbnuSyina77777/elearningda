<?php
$teacher = App\Models\Teacher::has('taughtClassrooms')->has('taughtSubjects')->first();
echo "Teacher ID: " . $teacher->id . "\n";
$classroom = $teacher->taughtClassrooms()->first();
$subject = $teacher->taughtSubjects()->first();
echo "Classroom ID: " . $classroom->id . "\n";
echo "Subject ID: " . $subject->id . "\n";

$foundTeacher = $subject->taughtBy()->whereHas('taughtClassrooms', function($q) use ($classroom) { 
    $q->where('classrooms.id', $classroom->id); 
})->first();

echo "Found Teacher ID: " . ($foundTeacher ? $foundTeacher->id : 'NO') . "\n";
