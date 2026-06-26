<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel profil siswa terhubung ke user dan kelas
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('classroom_id')->constrained('classrooms')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nisn', 20)->unique()->comment('Nomor Induk Siswa Nasional');
            $table->string('nis', 20)->unique()->comment('Nomor Induk Siswa (sekolah)');
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->char('gender', 1)->comment('L = Laki-laki, P = Perempuan');
            $table->date('birth_date')->nullable();
            $table->string('parent_name')->nullable()->comment('Nama orang tua/wali');
            $table->string('parent_phone', 20)->nullable()->comment('Nomor telepon orang tua/wali');
            $table->timestamps();
            $table->softDeletes();

            $table->index('classroom_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
