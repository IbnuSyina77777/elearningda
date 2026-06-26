<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        if (!$student) {
            return redirect('/')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $teachers = \App\Models\Teacher::whereHas('taughtClassrooms', function($q) use ($student) {
            $q->where('classrooms.id', $student->classroom_id);
        })->with(['taughtSubjects' => function($q) {
            $q->where('is_active', true);
        }, 'user'])->get();

        $subjects = collect();
        foreach($teachers as $teacher) {
            foreach($teacher->taughtSubjects as $subject) {
                $subject->teacher_name = $teacher->name ?? $teacher->user->name ?? 'Guru';
                $subjects->push($subject);
            }
        }
        $subjects = $subjects->unique('id')->sortBy('name');

        return view('student.subjects.index', compact('subjects'));
    }

    public function show(Subject $subject)
    {
        $student = auth()->user()->student;
        
        $teacher = \App\Models\Teacher::whereHas('taughtClassrooms', function($q) use ($student) {
            $q->where('classrooms.id', $student->classroom_id);
        })->whereHas('taughtSubjects', function($q) use ($subject) {
            $q->where('subjects.id', $subject->id);
        })->first();

        if (!$teacher) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
        }

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $yearId = $activeYear->id ?? null;

        $subject->teacher_name = $teacher->name ?? $teacher->user->name ?? 'Guru';

        $subject->setRelation('materials', \App\Models\Material::where('subject_id', $subject->id)
            ->where('classroom_id', $student->classroom_id)
            ->where('academic_year_id', $yearId)
            ->orderBy('order')
            ->get());
            
        $subject->setRelation('assignments', \App\Models\Assignment::where('subject_id', $subject->id)
            ->where('classroom_id', $student->classroom_id)
            ->where('academic_year_id', $yearId)
            ->orderBy('due_date', 'desc')
            ->get());
        
        return view('student.subjects.show', compact('subject'));
    }
}
