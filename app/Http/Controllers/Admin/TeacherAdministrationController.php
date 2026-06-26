<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\TeacherAdministration;
use Illuminate\Http\Request;

class TeacherAdministrationController extends Controller
{
    protected $requiredDocs = ['CP', 'TP', 'ATP', 'Modul Ajar', 'Prota', 'Promes', 'Buku Nilai'];

    public function index(Request $request)
    {
        // Get all teachers who are teaching at least one subject
        $teachers = Teacher::with('taughtSubjects', 'user')->whereHas('taughtSubjects')->get();
        
        $rekap = collect();

        foreach ($teachers as $teacher) {
            foreach ($teacher->taughtSubjects as $subject) {
                $docs = TeacherAdministration::where('teacher_id', $teacher->id)
                            ->where('subject_id', $subject->id)
                            ->get();
                
                $uploadedTypes = $docs->pluck('type')->toArray();
                $missingDocs = array_diff($this->requiredDocs, $uploadedTypes);
                
                $totalRequired = count($this->requiredDocs);
                $totalUploaded = $totalRequired - count($missingDocs);
                $percentage = round(($totalUploaded / $totalRequired) * 100);

                $rekap->push((object)[
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->name,
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->name,
                    'percentage' => $percentage,
                    'missing' => array_values($missingDocs),
                    'total_docs' => $docs->count()
                ]);
            }
        }

        // Search/Filter logic
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $rekap = $rekap->filter(function($item) use ($search) {
                return str_contains(strtolower($item->teacher_name), $search) || 
                       str_contains(strtolower($item->subject_name), $search);
            });
        }

        return view('admin.administrations.index', compact('rekap'));
    }

    public function show($teacher_id, $subject_id)
    {
        $teacher = Teacher::with('user')->findOrFail($teacher_id);
        $subject = Subject::findOrFail($subject_id);

        $administrations = TeacherAdministration::where('teacher_id', $teacher_id)
                                ->where('subject_id', $subject_id)
                                ->latest()
                                ->get();
                                
        $uploadedTypes = $administrations->pluck('type')->toArray();
        $missingDocs = array_diff($this->requiredDocs, $uploadedTypes);
        
        return view('admin.administrations.show', compact('teacher', 'subject', 'administrations', 'missingDocs'));
    }
}
