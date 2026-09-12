<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ima_indicators', function (Blueprint $table) {
            $table->integer('pengali')->default(1)->after('nama_indikator');
            $table->json('pilihan_parameter')->nullable()->after('pengali');
        });
    }

    public function down(): void
    {
        Schema::table('ima_indicators', function (Blueprint $table) {
            $table->dropColumn(['pengali', 'pilihan_parameter']);
        });
    }
};