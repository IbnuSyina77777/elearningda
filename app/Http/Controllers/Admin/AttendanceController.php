<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('name')->get();
        $selectedClassroomId = $request->query('classroom_id', $classrooms->first()->id ?? null);
        
        $subjects = Subject::orderBy('name')->get();
        $selectedSubjectId = $request->query('subject_id', $subjects->first()->id ?? null);

        $date = $request->query('date', now()->format('Y-m-d'));

        $attendances = collect();
        if ($selectedClassroomId && $selectedSubjectId && $date) {
            $attendances = Attendance::with(['student.user', 'teacher.user'])
                ->where('classroom_id', $selectedClassroomId)
                ->where('subject_id', $selectedSubjectId)
                ->where('date', $date)
                ->get();
        }

        // Summary
        $summary = [
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'alpa' => $attendances->where('status', 'alpa')->count(),
        ];

        return view('admin.attendances.index', compact('classrooms', 'subjects', 'selectedClassroomId', 'selectedSubjectId', 'date', 'attendances', 'summary'));
    }
}
