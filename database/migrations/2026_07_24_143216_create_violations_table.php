<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Guru yang mencatat
            $table->string('category'); // Ringan, Sedang, Berat
            $table->string('violation_name'); // Nama/jenis pelanggaran
            $table->integer('points'); // Bobot poin
            $table->date('date'); // Tanggal kejadian
            $table->text('description')->nullable(); // Keterangan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};