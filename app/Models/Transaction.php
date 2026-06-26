<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'bill_id',
        'payment_item_id',
        'amount',
        'payment_type',
        'snap_token',
        'snap_url',
        'status',
        'midtrans_response',
        'paid_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'            => 'decimal:2',
            'midtrans_response' => 'array',
            'paid_at'           => 'datetime',
            'expired_at'        => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Transaksi milik satu tagihan.
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Transaksi milik satu item pembayaran (cicilan).
     * Nullable — null jika bayar langsung (non-cicilan).
     */
    public function paymentItem(): BelongsTo
    {
        return $this->belongsTo(PaymentItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope: transaksi yang sudah selesai (sukses).
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope: berdasarkan rentang tanggal.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Format nominal transaksi sebagai Rupiah.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Label status yang human-readable.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Menunggu Pembayaran',
            'success'   => 'Berhasil',
            'failed'    => 'Gagal',
            'expired'   => 'Kadaluarsa',
            'challenge' => 'Perlu Verifikasi',
            default     => 'Tidak Diketahui',
        };
    }

    /**
     * Warna badge status untuk UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'success'   => 'success',
            'failed'    => 'danger',
            'expired'   => 'secondary',
            'challenge' => 'info',
            default     => 'secondary',
        };
    }

    /**
     * Apakah transaksi berhasil?
     */
    public function getIsSuccessAttribute(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Apakah transaksi masih bisa dibayar (pending)?
     */
    public function getIsPayableAttribute(): bool
    {
        return $this->status === 'pending' && $this->snap_token !== null;
    }

    /**
     * Alias untuk kolom database (memudahkan di view)
     */
    public function getPaymentDateAttribute()
    {
        return $this->paid_at ?? $this->created_at;
    }

    public function getReferenceNumberAttribute(): string
    {
        return $this->order_id ?? '-';
    }

    public function getPaymentMethodAttribute(): string
    {
        return $this->payment_type ?? 'unknown';
    }

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Generate order_id unik untuk Midtrans.
     * Format: INV-{YYYYMMDD}-{RANDOM_5_CHAR}
     */
    public static function generateOrderId(): string
    {
        do {
            $orderId = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (static::where('order_id', $orderId)->exists());

        return $orderId;
    }

    /**
     * Tandai transaksi sebagai berhasil dan update tagihan terkait.
     */
    public function markAsSuccess(array $midtransResponse = []): void
    {
        $this->update([
            'status'            => 'success',
            'midtrans_response' => $midtransResponse,
            'paid_at'           => now(),
        ]);

        // Update status pembayaran cicilan jika ada
        if ($this->payment_item_id) {
            $this->paymentItem->update(['status' => 'paid']);
        }

        // Recalculate bill status
        $this->bill->recalculateStatus();
    }

    /**
     * Tandai transaksi sebagai gagal.
     */
    public function markAsFailed(array $midtransResponse = []): void
    {
        $this->update([
            'status'            => 'failed',
            'midtrans_response' => $midtransResponse,
        ]);
    }

    /**
     * Tandai transaksi sebagai expired.
     */
    public function markAsExpired(array $midtransResponse = []): void
    {
        $this->update([
            'status'            => 'expired',
            'midtrans_response' => $midtransResponse,
        ]);
    }
}
