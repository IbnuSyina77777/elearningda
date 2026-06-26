<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return redirect('/')->with('error', 'Profil guru tidak ditemukan.');
        }

        $subjects = $teacher->taughtSubjects()->where('is_active', true)->get();
        $totalSubjects = $subjects->count();
        $totalMaterials = \App\Models\Material::where('teacher_id', $teacher->id)->count();
        $totalAssignments = \App\Models\Assignment::where('teacher_id', $teacher->id)->count();
        $pendingSubmissions = \App\Models\Submission::whereIn('assignment_id', 
            \App\Models\Assignment::where('teacher_id', $teacher->id)->pluck('id')
        )->whereNull('grade')->count();

        $announcements = \App\Models\Announcement::where('is_active', true)
            ->whereIn('target_audience', ['all', 'teachers'])
            ->latest()
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact('teacher', 'subjects', 'totalSubjects', 'totalMaterials', 'totalAssignments', 'pendingSubmissions', 'announcements'));
    }
}
