<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel master kategori pembayaran: PTS, PAS, Ujikom, Kunjungan Industri
     */
    public function up(): void
    {
        Schema::create('payment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama kategori: PTS, PAS, Ujikom, Kunjungan Industri');
            $table->string('code', 20)->unique()->comment('Kode unik: PTS, PAS, UJIKOM, KUNJIND');
            $table->text('description')->nullable();
            $table->decimal('default_amount', 15, 2)->default(0)->comment('Nominal default untuk kategori ini');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_categories');
    }
};
