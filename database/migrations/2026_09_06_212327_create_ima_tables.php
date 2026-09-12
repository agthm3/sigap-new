<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ima_dropdowns', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->string('kode')->nullable();
            $table->text('label');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ima_indicators', function (Blueprint $table) {
            $table->id();
            $table->integer('no_urut');
            $table->string('nama_indikator');
            $table->text('deskripsi_panduan')->nullable();
            $table->string('file_panduan_path')->nullable();
            $table->string('video_url')->nullable();
            $table->json('parameter_options')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ima_inovasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('kategori_ima', ['PRO IMA', 'PEMULA IMA']);
            
            $table->string('judul');
            $table->string('opd_unit')->nullable();
            $table->string('inisiator_daerah')->nullable();
            $table->string('inisiator_nama')->nullable();
            $table->string('koordinat', 300)->nullable();
            
            $table->string('operator_nama');
            $table->string('operator_jabatan')->nullable();
            $table->string('operator_wa');
            $table->string('operator_email')->nullable();

            $table->string('klasifikasi')->nullable();
            $table->string('jenis_inovasi')->nullable();
            $table->string('bentuk_inovasi_daerah')->nullable();
            $table->string('asta_cipta')->nullable();
            $table->string('program_prioritas')->nullable();
            $table->string('urusan_pemerintah')->nullable();
            $table->text('misi_walikota')->nullable();

            $table->date('waktu_uji_coba')->nullable();
            $table->date('waktu_penerapan')->nullable();
            $table->string('tahap_inovasi', 50)->nullable();
            $table->enum('perkembangan_inovasi', ['Ya', 'Tidak'])->nullable();

            $table->text('rancang_bangun')->nullable();
            $table->text('tujuan')->nullable();
            $table->text('manfaat')->nullable();
            $table->text('hasil_inovasi')->nullable();

            $table->string('anggaran_file')->nullable();
            $table->string('profil_bisnis_file')->nullable();
            $table->string('haki_file')->nullable();
            $table->string('penghargaan_file')->nullable();

            $table->string('asistensi_status')->default('Menunggu Verifikasi');
            $table->text('asistensi_note')->nullable();
            $table->foreignId('asistensi_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('asistensi_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ima_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ima_inovasi_id')->constrained('ima_inovasis')->onDelete('cascade');
            $table->foreignId('ima_indicator_id')->constrained('ima_indicators')->onDelete('cascade');
            
            $table->integer('no_urut');
            $table->string('parameter_label')->nullable();
            $table->integer('parameter_weight')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('link_url')->nullable();
            
            $table->string('review_status')->nullable();
            $table->text('review_note')->nullable();
            
            $table->timestamps();
        });

        Schema::create('ima_evidence_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ima_evidence_id')->constrained('ima_evidences')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_mime')->nullable();
            $table->integer('file_size')->nullable();
            
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('tentang')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ima_evidence_files');
        Schema::dropIfExists('ima_evidences');
        Schema::dropIfExists('ima_inovasis');
        Schema::dropIfExists('ima_indicators');
        Schema::dropIfExists('ima_dropdowns');
    }
};