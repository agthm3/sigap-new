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
        Schema::table('sigap_daftar_hadir_kegiatan', function (Blueprint $table) {
            // Menambahkan kolom nomor_surat setelah undangan_path
            $table->string('nomor_surat')->nullable()->after('undangan_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sigap_daftar_hadir_kegiatan', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn('nomor_surat');
        });
    }
};