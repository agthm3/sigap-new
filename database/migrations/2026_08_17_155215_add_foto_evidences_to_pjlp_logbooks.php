<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pjlp_logbooks', function (Blueprint $table) {
            // Menambahkan kolom json untuk menampung array foto
            $table->json('foto_evidences')->nullable()->after('foto_evidence');
        });
    }

    public function down(): void
    {
        Schema::table('pjlp_logbooks', function (Blueprint $table) {
            $table->dropColumn('foto_evidences');
        });
    }
};