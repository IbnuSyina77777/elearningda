<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\StudentsImport, 'dummy_students.xlsx')[0];
$header = array_shift($rows);

$successCount = 0;
$errorCount = 0;

foreach ($rows as $row) {
    $nisn = trim($row[0] ?? '');
    $nis = trim($row[1] ?? '');
    $name = trim($row[2] ?? '');
    $email = trim($row[3] ?? '');
    $gender = trim($row[4] ?? '');
    $gender = ($gender === 'L' || $gender === 'P') ? $gender : 'L';
    $phone = trim($row[5] ?? '');
    $address = trim($row[6] ?? '');
    $parent_name = trim($row[7] ?? '');
    $parent_phone = trim($row[8] ?? '');
    $classroom_name = trim($row[9] ?? '');

    if (empty($nisn) || empty($nis) || empty($name) || empty($email) || empty($classroom_name)) {
        echo "Missing required fields for NISN $nisn\n";
        $errorCount++;
        continue;
    }

    if (\App\Models\Student::where('nisn', $nisn)->orWhere('nis', $nis)->exists() || \App\Models\User::where('email', $email)->exists()) {
        echo "Student already exists (NISN: $nisn, NIS: $nis, Email: $email)\n";
        $errorCount++;
        continue;
    }

    $classroom = \App\Models\Classroom::where('name', $classroom_name)->first();
    if (!$classroom) {
        echo "Classroom $classroom_name not found\n";
        $errorCount++;
        continue;
    }

    echo "Would insert $name\n";
    $successCount++;
}

echo "Success: $successCount, Error: $errorCount\n";
