<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function show(Assignment $assignment)
    {
        $student = auth()->user()->student;
        if ($assignment->classroom_id !== $student->classroom_id) {
            abort(403, 'Akses ditolak.');
        }

        $assignment->load('subject', 'teacher.user');
        $submission = Submission::where('assignment_id', $assignment->id)
                                ->where('student_id', $student->id)
                                ->first();

        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $student = auth()->user()->student;
        if ($assignment->classroom_id !== $student->classroom_id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'file'  => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip|max:10240',
            'notes' => 'nullable|string|max:1000',
        ]);

        $existing = Submission::where('assignment_id', $assignment->id)
                              ->where('student_id', $student->id)
                              ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah mengumpulkan tugas ini.');
        }

        $file = $request->file('file');

        Submission::create([
            'assignment_id' => $assignment->id,
            'student_id'    => $student->id,
            'file_path'     => $file->store('submissions', 'public'),
            'file_name'     => $file->getClientOriginalName(),
            'notes'         => $validated['notes'],
            'submitted_at'  => now(),
        ]);

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}
