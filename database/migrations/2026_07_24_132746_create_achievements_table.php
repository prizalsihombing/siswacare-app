<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('title'); // Nama Prestasi
            $table->string('level'); // Tingkat (Sekolah, Kabupaten, Nasional, dll)
            $table->string('rank'); // Peringkat (Juara 1, 2, dll)
            $table->date('date'); // Tanggal perolehan
            $table->string('bukti')->nullable(); // Untuk upload file foto/pdf
            $table->text('description')->nullable(); // Keterangan opsional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};