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
        Schema::table('counselings', function (Blueprint $table) {
            $table->time('time')->after('date');
            $table->dropColumn(['status', 'notes']);
        });
    }

    public function down(): void
    {
        Schema::table('counselings', function (Blueprint $table) {
            $table->dropColumn('time');
            $table->enum('status', ['diproses', 'tindak lanjut', 'selesai'])->default('diproses');
            $table->text('notes')->nullable();
        });
    }
};
