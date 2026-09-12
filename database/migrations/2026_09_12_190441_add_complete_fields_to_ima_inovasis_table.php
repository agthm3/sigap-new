<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ima_inovasis', function (Blueprint $table) {
            // Cek dan tambahkan kolom jika belum ada
            if (!Schema::hasColumn('ima_inovasis', 'inisiator_daerah')) {
                $table->string('inisiator_daerah')->nullable()->after('opd_unit');
            }
            if (!Schema::hasColumn('ima_inovasis', 'inisiator_nama')) {
                $table->string('inisiator_nama')->nullable()->after('inisiator_daerah');
            }
            if (!Schema::hasColumn('ima_inovasis', 'koordinat')) {
                $table->string('koordinat', 300)->nullable()->after('inisiator_nama');
            }
            if (!Schema::hasColumn('ima_inovasis', 'tahap_inovasi')) {
                $table->string('tahap_inovasi')->nullable()->after('misi_walikota');
            }
            if (!Schema::hasColumn('ima_inovasis', 'waktu_uji_coba')) {
                $table->date('waktu_uji_coba')->nullable()->after('tahap_inovasi');
            }
            if (!Schema::hasColumn('ima_inovasis', 'waktu_penerapan')) {
                $table->date('waktu_penerapan')->nullable()->after('waktu_uji_coba');
            }
            if (!Schema::hasColumn('ima_inovasis', 'perkembangan_inovasi')) {
                $table->string('perkembangan_inovasi')->nullable()->after('waktu_penerapan');
            }
            if (!Schema::hasColumn('ima_inovasis', 'tujuan')) {
                $table->text('tujuan')->nullable()->after('rancang_bangun');
            }
            if (!Schema::hasColumn('ima_inovasis', 'manfaat')) {
                $table->text('manfaat')->nullable()->after('tujuan');
            }
            if (!Schema::hasColumn('ima_inovasis', 'hasil_inovasi')) {
                $table->text('hasil_inovasi')->nullable()->after('manfaat');
            }
            if (!Schema::hasColumn('ima_inovasis', 'sampul_file')) {
                $table->string('sampul_file')->nullable()->after('hasil_inovasi');
            }
            if (!Schema::hasColumn('ima_inovasis', 'haki_file')) {
                $table->string('haki_file')->nullable()->after('profil_bisnis_file');
            }
            if (!Schema::hasColumn('ima_inovasis', 'penghargaan_file')) {
                $table->string('penghargaan_file')->nullable()->after('haki_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ima_inovasis', function (Blueprint $table) {
            $table->dropColumn([
                'inisiator_daerah', 'inisiator_nama', 'koordinat',
                'tahap_inovasi', 'waktu_uji_coba', 'waktu_penerapan', 'perkembangan_inovasi',
                'tujuan', 'manfaat', 'hasil_inovasi', 'sampul_file',
                'haki_file', 'penghargaan_file'
            ]);
        });
    }
};