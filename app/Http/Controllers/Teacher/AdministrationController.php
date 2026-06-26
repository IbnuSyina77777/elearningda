<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherAdministration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdministrationController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return redirect('/')->with('error', 'Akses ditolak. Profil guru tidak ditemukan.');
        }

        // Ambil daftar mata pelajaran yang diajar guru ini
        $subjects = $teacher->taughtSubjects;

        $query = TeacherAdministration::where('teacher_id', $teacher->id)->with('subject');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $administrations = $query->latest()->paginate(15)->withQueryString();

        $types = ['CP', 'TP', 'ATP', 'Modul Ajar', 'Prota', 'Promes', 'Buku Nilai', 'Lainnya'];

        return view('teacher.administrations.index', compact('administrations', 'subjects', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'type'       => 'required|string|max:50',
            'title'      => 'required|string|max:255',
            'file'       => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'description'=> 'nullable|string',
        ]);

        $teacher = auth()->user()->teacher;

        $path = $request->file('file')->store('administrations', 'public');

        TeacherAdministration::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $request->subject_id,
            'type'       => $request->type,
            'title'      => $request->title,
            'file_path'  => $path,
            'description'=> $request->description,
        ]);

        return back()->with('success', 'Dokumen administrasi berhasil diunggah.');
    }

    public function destroy($id)
    {
        $teacher = auth()->user()->teacher;
        $admin = TeacherAdministration::where('teacher_id', $teacher->id)->findOrFail($id);

        if (Storage::disk('public')->exists($admin->file_path)) {
            Storage::disk('public')->delete($admin->file_path);
        }

        $admin->delete();

        return back()->with('success', 'Dokumen administrasi berhasil dihapus.');
    }
}
