<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'classroom.major'])
            ->where('status', 'alumni');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('graduation_year') && $request->graduation_year != '') {
            $query->where('graduation_year', $request->graduation_year);
        }

        $alumnis = $query->orderBy('graduation_year', 'desc')->paginate(10)->withQueryString();
        $graduationYears = Student::where('status', 'alumni')
            ->whereNotNull('graduation_year')
            ->distinct()
            ->pluck('graduation_year');

        return view('admin.alumni.index', compact('alumnis', 'graduationYears'));
    }
}
