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
        Schema::table('inovasis', function (Blueprint $table) {
            $table->string('misi_walikota')
                  ->nullable()
                  ->after('program_prioritas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inovasis', function (Blueprint $table) {
            $table->dropColumn('misi_walikota');
        });
    }
};