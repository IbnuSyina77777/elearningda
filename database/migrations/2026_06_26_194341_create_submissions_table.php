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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onUpdate('cascade');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->text('notes')->nullable();
            $table->integer('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->datetime('graded_at')->nullable();
            $table->timestamps();
            
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
