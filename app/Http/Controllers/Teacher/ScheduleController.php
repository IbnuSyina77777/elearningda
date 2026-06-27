<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $academicYear = AcademicYear::where('is_active', true)->first();

        $query = Schedule::with(['classroom', 'subject'])
                         ->where('teacher_id', $teacher->id);

        if ($academicYear) {
            $query->where('academic_year_id', $academicYear->id);
        }

        $schedules = $query->orderBy('start_time')->get();

        $groupedSchedules = [
            'Senin'  => $schedules->where('day_of_week', 'Senin'),
            'Selasa' => $schedules->where('day_of_week', 'Selasa'),
            'Rabu'   => $schedules->where('day_of_week', 'Rabu'),
            'Kamis'  => $schedules->where('day_of_week', 'Kamis'),
            'Jumat'  => $schedules->where('day_of_week', 'Jumat'),
            'Sabtu'  => $schedules->where('day_of_week', 'Sabtu'),
        ];

        return view('teacher.schedules.index', compact('groupedSchedules', 'academicYear'));
    }
}
