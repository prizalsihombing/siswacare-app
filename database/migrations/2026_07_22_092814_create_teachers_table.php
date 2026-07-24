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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nuptk')->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->string('phone')->nullable();
            $table->enum('role_type', ['Wali Kelas', 'Guru Mapel']);
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null'); // Relasi ke tabel kelas
            $table->string('subject')->nullable();
            $table->enum('status', ['Aktif', 'Cuti', 'Keluar'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
