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
        Schema::create('surat_keluars', function (Blueprint $table) {
            $table->id();
            
            // Pengelompokan tahun takwim & tanggal surat
            $table->unsignedSmallInteger('tahun')->index()->comment('Tahun takwim buku agenda (misal 2026)');
            $table->date('tanggal')->index()->comment('Tanggal surat keluar');
            
            // Nomor urut baris di buku agenda (reset tiap tahun)
            $table->unsignedInteger('nomor_urut')->index()->comment('Nomor urut baris agenda (misal: 291)');
            
            // Format Nomor Berkas / Klasifikasi (Permendagri 83/2022 atau manual)
            // Dibuat nullable karena saat slot 10 baris pertama di-generate, baris masih berstatus slot_kosong
            $table->string('nomor_berkas', 50)->nullable()->comment('Kode klasifikasi surat, misal: 070');
            
            // Format lengkap surat setelah dirakit: 070/291/BPPD/III/2026
            $table->string('nomor_surat_lengkap', 150)->nullable()->index();
            
            // 5 Kolom inti buku manual
            $table->text('alamat_penerima')->nullable()->comment('Tujuan / alamat instansi penerima surat');
            $table->text('perihal')->nullable()->comment('Perihal / isi ringkas surat');
            
            // Kolom Pembuat (User yang mengambil/menerbitkan nomor surat)
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('ID akun user yang mengambil nomor');

            // Status Baris Buku Agenda
            // - slot_kosong : 10 baris cadangan per tanggal untuk kebutuhan tanggal mundur
            // - terbit      : Nomor surat resmi sudah terisi & aktif
            // - batal       : Nomor surat dibatalkan resmi (tidak boleh ditimpa agar nomor urut tidak hilang)
            $table->enum('status', ['slot_kosong', 'terbit', 'batal'])
                  ->default('slot_kosong')
                  ->index();

            // Lampiran berkas scan PDF (jika ada fisik surat yang ditandatangani)
            $table->string('file_surat')->nullable();
            
            // Catatan tambahan / alasan jika dibatalkan
            $table->text('catatan')->nullable();

            $table->timestamps();

            // Integritas data: Dalam 1 tahun takwim yang sama, nomor urut tidak boleh duplikat/ganda
            $table->unique(['tahun', 'nomor_urut'], 'unique_tahun_nomor_urut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluars');
    }
};