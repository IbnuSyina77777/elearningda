<?php

namespace App\Imports;

use App\Models\Schedule;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class SchedulesImport implements ToModel, WithHeadingRow
{
    protected $academicYearId;

    public function __construct($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['hari']) || empty($row['nama_kelas'])) {
            return null;
        }

        // Find relations by name
        $classroom = Classroom::where('name', $row['nama_kelas'])->first();
        $subject = Subject::where('name', $row['mata_pelajaran'])->first();
        
        // Find teacher by user name
        $teacherName = $row['nama_guru'];
        $teacher = Teacher::whereHas('user', function($q) use ($teacherName) {
            $q->where('name', $teacherName);
        })->first();

        // If any relation is missing, throw exception to inform the user
        if (!$classroom) {
            throw new \Exception("Kelas '{$row['nama_kelas']}' tidak ditemukan di sistem.");
        }
        if (!$subject) {
            throw new \Exception("Mata Pelajaran '{$row['mata_pelajaran']}' tidak ditemukan di sistem.");
        }
        if (!$teacher) {
            throw new \Exception("Guru dengan nama '{$row['nama_guru']}' tidak ditemukan di sistem.");
        }

        // Format time (Excel sometimes gives time as fraction of day, or H:i:s. We'll assume text H:i for now)
        $startTime = $this->parseTime($row['jam_mulai']);
        $endTime = $this->parseTime($row['jam_selesai']);

        $dayOfWeek = ucfirst(strtolower($row['hari']));

        // Validasi Double Data: Cek apakah jadwal yang sama persis sudah ada di database
        $exists = Schedule::where([
            'academic_year_id' => $this->academicYearId,
            'classroom_id'     => $classroom->id,
            'day_of_week'      => $dayOfWeek,
            'start_time'       => $startTime,
        ])->exists();

        if ($exists) {
            // Skip baris ini karena datanya duplikat/sudah ada
            return null;
        }

        return new Schedule([
            'academic_year_id' => $this->academicYearId,
            'classroom_id'     => $classroom->id,
            'subject_id'       => $subject->id,
            'teacher_id'       => $teacher->id,
            'day_of_week'      => $dayOfWeek,
            'start_time'       => $startTime,
            'end_time'         => $endTime,
        ]);
    }

    private function parseTime($time)
    {
        if (is_numeric($time)) {
            // Convert Excel time fraction to H:i
            $seconds = round($time * 86400);
            return gmdate('H:i', $seconds);
        }
        return date('H:i', strtotime($time));
    }
}
