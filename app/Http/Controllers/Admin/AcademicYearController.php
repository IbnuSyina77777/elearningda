<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::latest()->get();
        return view('admin.academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'semester'   => 'required|in:Ganjil,Genap',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_active'  => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['is_active']) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($validated);

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun Ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'semester'   => 'required|in:Ganjil,Genap',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_active'  => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['is_active'] && !$academicYear->is_active) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update($validated);

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return back()->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif.');
        }

        $academicYear->delete();
        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun Ajaran berhasil dihapus.');
    }
}
