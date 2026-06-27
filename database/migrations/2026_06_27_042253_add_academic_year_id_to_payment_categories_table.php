<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_categories', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('id')->constrained('academic_years')->nullOnDelete();
            
            // Hapus unique constraint lama
            $table->dropUnique('payment_categories_code_unique');
            
            // Tambahkan unique constraint baru
            $table->unique(['code', 'academic_year_id'], 'payment_cats_code_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_categories', function (Blueprint $table) {
            $table->dropUnique('payment_cats_code_year_unique');
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
            $table->unique('code', 'payment_categories_code_unique');
        });
    }
};
