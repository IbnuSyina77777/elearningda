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
        Schema::table('attendance_weights', function (Blueprint $table) {
            $table->unique(['subject_id', 'classroom_id', 'teacher_id', 'academic_year_id'], 'att_weights_unique_year');
            $table->dropUnique('att_weights_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_weights', function (Blueprint $table) {
            $table->dropUnique('att_weights_unique_year');
            $table->unique(['subject_id', 'classroom_id', 'teacher_id'], 'att_weights_unique');
        });
    }
};
