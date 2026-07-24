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
            $table->enum('status', ['diproses', 'tindak lanjut', 'selesai'])->default('diproses')->after('time');
        });
    }

    public function down(): void
    {
        Schema::table('counselings', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
