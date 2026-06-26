<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel tahun ajaran (2025/2026 Ganjil, 2025/2026 Genap, dll.)
     */
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->comment('Contoh: 2025/2026 Ganjil');
            $table->string('semester', 10)->comment('Ganjil / Genap');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false)->comment('Hanya 1 yang aktif');
            $table->timestamps();

            $table->unique(['name', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
