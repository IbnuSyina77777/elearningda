<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel cicilan/item pembayaran — memecah 1 bill menjadi beberapa termin
     * Contoh: PTS Rp 500.000 → Cicilan 1: Rp 250.000 + Cicilan 2: Rp 250.000
     */
    public function up(): void
    {
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedSmallInteger('installment_number')->default(1)->comment('Nomor cicilan: 1, 2, 3, ...');
            $table->decimal('amount', 15, 2)->comment('Nominal cicilan');
            $table->string('status', 20)->default('unpaid')->comment('unpaid, paid');
            $table->date('due_date')->nullable()->comment('Jatuh tempo cicilan');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->unique(['bill_id', 'installment_number'], 'payment_items_unique_bill_installment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};
