<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel master kelas (X-TKJ-1, XI-RPL-2, dll.) terhubung ke jurusan
     */
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained('majors')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('name', 20)->comment('Nama kelas: X-TKJ-1, XI-RPL-2');
            $table->string('level', 5)->comment('Tingkat: X, XI, XII');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['major_id', 'name']);
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
