<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'installment_number',
        'amount',
        'status',
        'due_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'             => 'decimal:2',
            'installment_number' => 'integer',
            'due_date'           => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Item pembayaran milik satu tagihan.
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Item pembayaran bisa memiliki banyak transaksi.
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

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: cicilan berikutnya yang harus dibayar.
     */
    public function scopeNextDue($query)
    {
        return $query->where('status', 'unpaid')
                     ->orderBy('installment_number', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Format nominal cicilan sebagai Rupiah.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Label cicilan: "Cicilan ke-1", "Cicilan ke-2", dst.
     */
    public function getLabelAttribute(): string
    {
        return "Cicilan ke-{$this->installment_number}";
    }

    /**
     * Apakah cicilan ini sudah dibayar?
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }
}
