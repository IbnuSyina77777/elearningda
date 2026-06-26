<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'NISN',
            'NIS',
            'Nama Lengkap',
            'Email',
            'Jenis Kelamin',
            'Telepon',
            'Alamat',
            'Nama Orang Tua',
            'Telepon Orang Tua',
            'Kelas'
        ];
    }

    public function map($student): array
    {
        return [
            $student->nisn,
            $student->nis,
            $student->name,
            $student->email,
            $student->gender,
            $student->phone,
            $student->address,
            $student->parent_name,
            $student->parent_phone,
            $student->classroom ? $student->classroom->name : ''
        ];
    }
}
