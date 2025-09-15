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
 Schema::create('inovasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('judul');
            $table->string('opd_unit')->nullable();
            $table->string('inisiator_daerah')->nullable();
            $table->string('inisiator_nama')->nullable();
            $table->string('koordinat')->nullable();
            $table->string('klasifikasi')->nullable();
            $table->string('jenis_inovasi')->nullable();
            $table->string('bentuk_inovasi_daerah')->nullable();
            $table->string('asta_cipta')->nullable();
            $table->string('program_prioritas')->nullable();
            $table->string('urusan_pemerintah')->nullable();
            $table->date('waktu_uji_coba')->nullable();
            $table->date('waktu_penerapan')->nullable();

            $table->string('tahap_inovasi')->nullable();
            $table->string('perkembangan_inovasi')->nullable();

            $table->longText('rancang_bangun')->nullable();
            $table->longText('tujuan')->nullable();
            $table->longText('manfaat')->nullable();
            $table->longText('hasil_inovasi')->nullable();

            $table->string('anggaran_file')->nullable();
            $table->string('profil_bisnis_file')->nullable();
            $table->string('haki_file')->nullable();
            $table->string('penghargaan_file')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['judul','opd_unit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inovasis');
    }
};
