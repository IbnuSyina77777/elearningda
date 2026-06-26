<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'classroom_id',
        'nisn',
        'nis',
        'phone',
        'address',
        'photo',
        'gender',
        'birth_date',
        'parent_name',
        'parent_phone',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Siswa terhubung ke satu user (akun login).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Siswa berada di satu kelas.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Siswa memiliki banyak tagihan.
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Siswa memiliki banyak transaksi melalui tagihan.
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, Bill::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: siswa dengan tagihan belum lunas.
     */
    public function scopeHasUnpaidBills($query)
    {
        return $query->whereHas('bills', function ($q) {
            $q->where('status', '!=', 'paid');
        });
    }

    /**
     * Scope: filter berdasarkan kelas.
     */
    public function scopeInClassroom($query, int $classroomId)
    {
        return $query->where('classroom_id', $classroomId);
    }

    /**
     * Scope: filter berdasarkan jurusan melalui kelas.
     */
    public function scopeInMajor($query, int $majorId)
    {
        return $query->whereHas('classroom', function ($q) use ($majorId) {
            $q->where('major_id', $majorId);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Nama lengkap dari user yang terhubung.
     */
    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * Email dari user yang terhubung.
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    /**
     * Total tagihan belum lunas.
     */
    public function getTotalUnpaidAttribute(): float
    {
        return $this->bills()
            ->where('status', '!=', 'paid')
            ->sum(\Illuminate\Support\Facades\DB::raw('amount - total_paid'));
    }
}
