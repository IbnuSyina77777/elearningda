<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * User memiliki satu profil siswa (jika role = student).
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * User memiliki satu profil guru (jika role = teacher).
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: filter berdasarkan role.
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeTeachers($query)
    {
        return $query->where('role', 'teacher');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Apakah user adalah admin?
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Apakah user adalah siswa?
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Apakah user adalah guru?
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }
}
