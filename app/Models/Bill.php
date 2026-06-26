<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'payment_category_id',
        'academic_year_id',
        'amount',
        'total_paid',
        'status',
        'due_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
            'total_paid' => 'decimal:2',
            'due_date'   => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Tagihan milik satu siswa.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Tagihan termasuk dalam satu kategori pembayaran.
     */
    public function paymentCategory(): BelongsTo
    {
        return $this->belongsTo(PaymentCategory::class);
    }

    /**
     * Tagihan termasuk dalam satu tahun ajaran.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Tagihan memiliki banyak item pembayaran (cicilan).
     */
    public function paymentItems(): HasMany
    {
        return $this->hasMany(PaymentItem::class);
    }

    /**
     * Tagihan memiliki banyak transaksi.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: tagihan belum lunas.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope: tagihan lunas sebagian.
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope: tagihan sudah lunas.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: tagihan yang belum lunas (unpaid + partial).
     */
    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', ['unpaid', 'partial']);
    }

    /**
     * Scope: tagihan jatuh tempo.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->where('status', '!=', 'paid');
    }

    /**
     * Scope: filter berdasarkan tahun ajaran.
     */
    public function scopeForAcademicYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Sisa tagihan yang belum dibayar.
     */
    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->amount - (float) $this->total_paid;
    }

    /**
     * Format nominal tagihan sebagai Rupiah.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Format total yang sudah dibayar sebagai Rupiah.
     */
    public function getFormattedPaidAttribute(): string
    {
        return 'Rp ' . number_format($this->total_paid, 2, ',', '.');
    }

    /**
     * Format sisa tagihan sebagai Rupiah.
     */
    public function getFormattedRemainingAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_amount, 2, ',', '.');
    }

    /**
     * Persentase pembayaran (0-100).
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ((float) $this->amount === 0.0) {
            return 0;
        }

        return round(((float) $this->total_paid / (float) $this->amount) * 100, 2);
    }

    /**
     * Apakah tagihan sudah lunas?
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Apakah tagihan sudah jatuh tempo?
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->is_paid;
    }

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Update status tagihan berdasarkan total yang sudah dibayar.
     * Dipanggil setelah transaksi berhasil.
     */
    public function recalculateStatus(): void
    {
        $this->total_paid = $this->transactions()
            ->where('status', 'success')
            ->sum('amount');

        if ((float) $this->total_paid >= (float) $this->amount) {
            $this->status = 'paid';
        } elseif ((float) $this->total_paid > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();
    }
}
