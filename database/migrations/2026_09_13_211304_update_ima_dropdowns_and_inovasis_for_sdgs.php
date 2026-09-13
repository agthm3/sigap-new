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
        // 1. Tambah kolom gambar ikon, warna tema, dan deskripsi di tabel ima_dropdowns
        Schema::table('ima_dropdowns', function (Blueprint $table) {
            if (!Schema::hasColumn('ima_dropdowns', 'icon_path')) {
                $table->string('icon_path')->nullable()->after('kode');
            }
            if (!Schema::hasColumn('ima_dropdowns', 'warna')) {
                $table->string('warna', 50)->nullable()->default('#E5243B')->after('icon_path');
            }
            if (!Schema::hasColumn('ima_dropdowns', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('label');
            }
        });

        // 2. Tambah kolom pilihan SDGs & uraian keterkaitan, serta ubah anggaran_file jadi nullable di ima_inovasis
        Schema::table('ima_inovasis', function (Blueprint $table) {
            if (!Schema::hasColumn('ima_inovasis', 'sdgs_pilihan')) {
                $table->json('sdgs_pilihan')->nullable()->after('hasil_inovasi');
            }
            if (!Schema::hasColumn('ima_inovasis', 'sdgs_keterkaitan')) {
                $table->longText('sdgs_keterkaitan')->nullable()->after('sdgs_pilihan');
            }
            // Ubah anggaran_file menjadi nullable jika sebelumnya belum
            if (Schema::hasColumn('ima_inovasis', 'anggaran_file')) {
                $table->string('anggaran_file')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ima_dropdowns', function (Blueprint $table) {
            $table->dropColumn(['icon_path', 'warna', 'deskripsi']);
        });

        Schema::table('ima_inovasis', function (Blueprint $table) {
            $table->dropColumn(['sdgs_pilihan', 'sdgs_keterkaitan']);
        });
    }
};