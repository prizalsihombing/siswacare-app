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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relasi ke tabel users untuk login
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade'); // Relasi ke kelas
            $table->string('nisn')->unique(); // NISN sebagai username & password default
            $table->string('name'); // Nama lengkap siswa
            $table->enum('gender', ['L', 'P']); // Jenis kelamin
            $table->string('guardian_phone')->nullable(); // No HP Wali
            $table->enum('status', ['Aktif', 'Keluar'])->default('Aktif'); // Status siswa
            $table->timestamps();
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
