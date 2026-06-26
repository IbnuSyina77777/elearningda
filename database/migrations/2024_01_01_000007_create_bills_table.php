<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel tagihan siswa (1 record = 1 tagihan, mis: PTS semester ganjil 2025/2026)
     */
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_category_id')->constrained('payment_categories')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('amount', 15, 2)->comment('Total nominal tagihan');
            $table->decimal('total_paid', 15, 2)->default(0)->comment('Total yang sudah dibayar');
            $table->string('status', 20)->default('unpaid')->comment('unpaid, partial, paid');
            $table->date('due_date')->nullable()->comment('Tanggal jatuh tempo');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk query performance
            $table->index('status');
            $table->index('due_date');
            $table->index(['student_id', 'academic_year_id']);

            // Prevent duplicate: 1 siswa, 1 kategori, 1 tahun ajaran = 1 tagihan
            $table->unique(['student_id', 'payment_category_id', 'academic_year_id'], 'bills_unique_student_category_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
