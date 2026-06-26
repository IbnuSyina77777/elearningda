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
            $table->integer('pts_weight')->default(0)->after('weight');
            $table->integer('pas_weight')->default(0)->after('pts_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_weights', function (Blueprint $table) {
            $table->dropColumn(['pts_weight', 'pas_weight']);
        });
    }
};
