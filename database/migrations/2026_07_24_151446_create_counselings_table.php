<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('violation_id')->nullable()->constrained('violations')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Guru BK/Admin pencatat
            $table->date('date');
            $table->enum('status', ['diproses', 'tindak lanjut', 'selesai'])->default('diproses');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselings');
    }
};