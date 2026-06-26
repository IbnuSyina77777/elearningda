<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'major_id',
        'name',
        'level',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Kelas milik satu jurusan.
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Kelas memiliki satu wali kelas (opsional).
     */
    public function homeroomTeacher()
    {
        return $this->hasOne(Teacher::class, 'classroom_id');
    }

    /**
     * Kelas memiliki banyak siswa.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Nama lengkap kelas beserta jurusan.
     * Contoh: "X-TKJ-1 (Teknik Komputer Jaringan)"
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->major->name})";
    }
}
