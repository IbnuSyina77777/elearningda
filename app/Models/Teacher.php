<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nip',
        'phone',
        'address',
        'photo',
        'specialization',
        'position',
        'classroom_id',
    ];

    public function homeroomClass(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function taughtClassrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_teacher', 'teacher_id', 'classroom_id')->withTimestamps();
    }

    public function taughtSubjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id')->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }
}
