<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun')->index();
            $table->unsignedInteger('nomor_agenda')->index(); // Nomor urut buku tahunan

            // Tanggal
            $table->date('tanggal_terima')->index();
            $table->date('tanggal_surat');

            // Data Surat
            $table->string('asal_surat'); // Surat Dari
            $table->string('nomor_surat_masuk'); // Nomor fisik surat
            $table->string('tingkat_surat')->default('Biasa'); // Biasa, Penting, Segera, Rahasia
            $table->text('perihal');

            // Unit Pengolah, Penerima & TTD Canvas
            $table->string('unit_pengolah'); // Subbagian / Bidang pengolah
            $table->foreignId('diterima_oleh')->constrained('users')->cascadeOnDelete();
            $table->longText('ttd_penerima')->nullable(); // Base64 data URL canvas PNG

            // Lampiran PDF Scan
            $table->string('file_surat')->nullable();

            $table->timestamps();

            $table->unique(['tahun', 'nomor_agenda']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_masuks');
    }
};