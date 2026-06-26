<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'level',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function taughtBy()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher', 'subject_id', 'teacher_id')->withTimestamps();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class)->orderBy('order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class)->orderBy('due_date', 'desc');
    }
}
