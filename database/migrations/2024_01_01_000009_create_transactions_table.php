<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel transaksi Midtrans — setiap percobaan pembayaran tercatat di sini
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 50)->unique()->comment('Format: INV-{YYYYMMDD}-{RANDOM}');
            $table->foreignId('bill_id')->constrained('bills')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_item_id')->nullable()->constrained('payment_items')->restrictOnDelete()->cascadeOnUpdate()
                  ->comment('Null jika bayar langsung (bukan cicilan)');
            $table->decimal('amount', 15, 2)->comment('Nominal yang dibayarkan');
            $table->string('payment_type', 50)->nullable()->comment('bank_transfer, gopay, shopeepay, qris, dll.');
            $table->string('snap_token')->nullable()->comment('Token dari Midtrans Snap');
            $table->string('snap_url')->nullable()->comment('URL redirect Midtrans Snap');
            $table->string('status', 20)->default('pending')->comment('pending, success, failed, expired, challenge');
            $table->json('midtrans_response')->nullable()->comment('Raw JSON response dari Midtrans webhook');
            $table->timestamp('paid_at')->nullable()->comment('Waktu pembayaran berhasil');
            $table->timestamp('expired_at')->nullable()->comment('Waktu kadaluarsa transaksi');
            $table->timestamps();

            // Indexes untuk query performance
            $table->index('status');
            $table->index('snap_token');
            $table->index('paid_at');
            $table->index(['bill_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
