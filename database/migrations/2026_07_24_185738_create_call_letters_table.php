<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->text('problem'); // Menyimpan masalah/pelanggaran
            $table->date('date'); // Tgl Pelaksanaan
            $table->time('time'); // Pukul
            $table->text('letter_content'); // Isi teks SPO otomatis
            $table->enum('status', ['Belum Hadir', 'Sudah Hadir', 'Penjadwalan Ulang'])->default('Belum Hadir');
            $table->text('notes')->nullable(); // Keterangan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_letters');
    }
};