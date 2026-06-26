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
        Schema::table('exam_scores', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('student_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('attendance_weights', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('teacher_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_scores', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });

        Schema::table('attendance_weights', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });
    }
};
