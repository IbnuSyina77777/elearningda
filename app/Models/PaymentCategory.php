<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'name',
        'code',
        'semester',
        'description',
        'default_amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_amount' => 'decimal:2',
            'is_active'      => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Kategori memiliki banyak tagihan.
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
    /**
     * Kategori terhubung ke satu tahun ajaran (opsional).
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Format nominal default sebagai Rupiah.
     * Contoh: "Rp 500.000,00"
     */
    public function getFormattedDefaultAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->default_amount, 2, ',', '.');
    }

    /**
     * Mendapatkan jenjang kelas target berdasarkan semester.
     * Semester 1 & 2 -> X
     * Semester 3 & 4 -> XI
     * Semester 5 & 6 -> XII
     */
    public function getTargetLevelAttribute(): ?string
    {
        if (in_array($this->semester, [1, 2])) return 'X';
        if (in_array($this->semester, [3, 4])) return 'XI';
        if (in_array($this->semester, [5, 6])) return 'XII';
        return null;
    }
}
