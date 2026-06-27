<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchedulesExport;
use App\Imports\SchedulesImport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::all();
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        $query = Schedule::with(['classroom', 'subject', 'teacher.user', 'academicYear'])
                         ->when($academicYear, fn($q) => $q->where('academic_year_id', $academicYear->id));

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        $schedules = $query->orderBy('start_time')->get();
        
        // Group by day 
        $groupedSchedules = [
            'Senin'  => $schedules->where('day_of_week', 'Senin'),
            'Selasa' => $schedules->where('day_of_week', 'Selasa'),
            'Rabu'   => $schedules->where('day_of_week', 'Rabu'),
            'Kamis'  => $schedules->where('day_of_week', 'Kamis'),
            'Jumat'  => $schedules->where('day_of_week', 'Jumat'),
            'Sabtu'  => $schedules->where('day_of_week', 'Sabtu'),
        ];

        return view('admin.schedules.index', compact('groupedSchedules', 'classrooms', 'academicYear'));
    }

    public function create()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            return redirect()->route('admin.schedules.index')->with('error', 'Tidak ada Tahun Ajaran yang aktif.');
        }

        $classrooms = Classroom::all();
        $subjects = Subject::with('taughtBy.user')->get();
        $teachers = Teacher::with('user')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('admin.schedules.create', compact('classrooms', 'subjects', 'teachers', 'days', 'academicYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id'   => 'required|exists:subjects,id',
            'teacher_id'   => 'required|exists:teachers,id',
            'day_of_week'  => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
        ]);

        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran aktif.');
        }

        // Cek Double Data
        $exists = Schedule::where([
            'academic_year_id' => $academicYear->id,
            'classroom_id'     => $request->classroom_id,
            'day_of_week'      => $request->day_of_week,
            'start_time'       => $request->start_time,
        ])->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Jadwal pelajaran di kelas dan hari/jam tersebut sudah ada (Bentrok).');
        }

        Schedule::create($request->all() + ['academic_year_id' => $academicYear->id]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule)
    {
        $classrooms = Classroom::all();
        $subjects = Subject::with('taughtBy.user')->get();
        $teachers = Teacher::with('user')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('admin.schedules.edit', compact('schedule', 'classrooms', 'subjects', 'teachers', 'days'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id'   => 'required|exists:subjects,id',
            'teacher_id'   => 'required|exists:teachers,id',
            'day_of_week'  => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }

    public function export()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran yang aktif.');
        }

        return Excel::download(new SchedulesExport($academicYear->id), 'jadwal_pelajaran_' . date('Ymd_His') . '.xlsx');
    }

    public function showImportForm()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            return redirect()->route('admin.schedules.index')->with('error', 'Tidak ada Tahun Ajaran yang aktif.');
        }

        return view('admin.schedules.import', compact('academicYear'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // Up to 5MB
        ]);

        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran yang aktif.');
        }

        try {
            Excel::import(new SchedulesImport($academicYear->id), $request->file('file'));
            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $export = new class implements FromArray, WithHeadings {
            public function array(): array
            {
                return [
                    ['Senin', '07:15', '08:45', 'X RPL 1', 'Matematika', 'Budi Santoso'],
                    ['Selasa', '09:00', '10:30', 'X RPL 1', 'Bahasa Inggris', 'Siti Aminah'],
                ];
            }
            public function headings(): array
            {
                return ['hari', 'jam_mulai', 'jam_selesai', 'nama_kelas', 'mata_pelajaran', 'nama_guru'];
            }
        };

        return Excel::download($export, 'template_jadwal_pelajaran.xlsx');
    }
}
