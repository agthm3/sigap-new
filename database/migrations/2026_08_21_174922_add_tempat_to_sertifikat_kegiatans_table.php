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
        Schema::table('sertifikat_kegiatans', function (Blueprint $table) {
            // Cek jika kolom 'tempat' belum ada, baru tambahkan
            if (!Schema::hasColumn('sertifikat_kegiatans', 'tempat')) {
                $table->string('tempat', 255)->nullable()->after('tanggal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sertifikat_kegiatans', function (Blueprint $table) {
            if (Schema::hasColumn('sertifikat_kegiatans', 'tempat')) {
                $table->dropColumn('tempat');
            }
        });
    }
};