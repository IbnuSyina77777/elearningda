<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'classroom_id',
        'teacher_id',
        'student_id',
        'academic_year_id',
        'pts_score',
        'pas_score',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
