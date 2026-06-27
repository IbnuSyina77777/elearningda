<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SchedulesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $academicYearId;

    public function __construct($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    public function collection()
    {
        return Schedule::with(['classroom', 'subject', 'teacher.user'])
            ->where('academic_year_id', $this->academicYearId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Hari',
            'Jam Mulai',
            'Jam Selesai',
            'Nama Kelas',
            'Mata Pelajaran',
            'Nama Guru',
        ];
    }

    public function map($schedule): array
    {
        return [
            $schedule->day_of_week,
            \Carbon\Carbon::parse($schedule->start_time)->format('H:i'),
            \Carbon\Carbon::parse($schedule->end_time)->format('H:i'),
            $schedule->classroom->name ?? '-',
            $schedule->subject->name ?? '-',
            $schedule->teacher->user->name ?? '-',
        ];
    }
}
