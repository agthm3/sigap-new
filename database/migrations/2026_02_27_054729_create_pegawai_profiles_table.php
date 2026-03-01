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
        Schema::create('pegawai_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // identitas
            $table->string('nik')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('golongan_darah')->nullable();

            // alamat
            $table->text('alamat_ktp')->nullable();
            $table->text('alamat_domisili')->nullable();

            // administrasi
            $table->string('npwp')->nullable();
            $table->string('bpjs_kesehatan')->nullable();
            $table->string('bpjs_ketenagakerjaan')->nullable();

            // kepegawaian
            $table->string('status_pegawai')->nullable(); // PNS/PPPK
            $table->string('jabatan')->nullable();
            $table->string('golongan')->nullable();
            $table->date('tmt_pns')->nullable();
            $table->string('atasan_langsung')->nullable();
            $table->string('nip_baru')->nullable();
            $table->string('nip_lama')->nullable();
            $table->string('golongan_ruang')->nullable();
            $table->date('tmt_golongan')->nullable();
            $table->integer('masa_kerja_tahun')->nullable();
            $table->integer('masa_kerja_bulan')->nullable();
            $table->date('tmt_jabatan')->nullable();
            $table->string('eselon')->nullable();
            $table->string('jabatan_struktural')->nullable();
            $table->string('jabatan_fungsional')->nullable();
            $table->string('jabatan_teknis')->nullable();
            $table->string('unor')->nullable();

            $table->string('pendidikan_terakhir')->nullable();
            $table->string('jurusan')->nullable();
            $table->integer('tahun_lulus')->nullable();

            $table->string('nama_sertifikat')->nullable();
            $table->string('bidang_sertifikat')->nullable();
            $table->integer('tahun_sertifikat')->nullable();

            $table->text('keterangan')->nullable();
            $table->string('bank_nama')->default('Bank Sulselbar');
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_rekening')->nullable();

            $table->string('nama_pasangan')->nullable();
            $table->string('pekerjaan_pasangan')->nullable();
            $table->integer('jumlah_anak')->nullable();
            $table->string('kontak_darurat')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_profiles');
    }
};
