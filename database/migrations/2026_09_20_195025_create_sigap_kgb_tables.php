<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Acuan Gaji Pokok (PNS & PPPK)
        Schema::create('kgb_gaji_pokok', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_pegawai', ['pns', 'pppk'])->index();
            $table->string('golongan', 20)->index(); // Contoh: 'I/a', 'III/a', atau 'IX', 'X'
            $table->unsignedTinyInteger('masa_kerja')->index(); // 0 s.d. 32 tahun
            $table->unsignedBigInteger('nominal'); // Nominal Gaji Pokok (Rp)
            $table->string('regulasi')->default('PP 5/2024'); // Dasar aturan
            $table->timestamps();

            $table->unique(['jenis_pegawai', 'golongan', 'masa_kerja', 'regulasi'], 'kgb_gaji_unique');
        });

        // 2. Tabel Transaksi / Riwayat KGB Pegawai
        Schema::create('kgb_riwayat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('jenis_pegawai', ['pns', 'pppk'])->default('pns');
            $table->string('pangkat_golongan', 50); // Contoh: Penata Muda / III/a atau Ahli Pertama / IX
            $table->string('jabatan')->nullable();
            
            // Data SK KGB Terakhir (Lama)
            $table->string('nomor_sk_lama');
            $table->date('tanggal_sk_lama');
            $table->date('tmt_lama');
            $table->unsignedTinyInteger('mkg_tahun_lama');
            $table->unsignedTinyInteger('mkg_bulan_lama')->default(0);
            $table->unsignedBigInteger('gaji_pokok_lama');
            $table->string('pejabat_penetap')->nullable(); // misal: Walikota Makassar / Kepala BRIDA

            // Data Kalkulasi Otomatis (KGB Baru)
            $table->date('tmt_baru'); // tmt_lama + 2 tahun
            $table->unsignedTinyInteger('mkg_tahun_baru'); // mkg_tahun_lama + 2 tahun
            $table->unsignedTinyInteger('mkg_bulan_baru')->default(0);
            $table->unsignedBigInteger('gaji_pokok_baru'); // Auto-lookup dari kgb_gaji_pokok

            // Status Pengusulan
            // 'menunggu': belum masuk masa usul
            // 'siap_usul': H-60 s/d jatuh tempo
            // 'proses': sedang diverifikasi/diajukan ke BKPSDM
            // 'selesai': SK KGB baru sudah terbit
            $table->enum('status', ['menunggu', 'siap_usul', 'proses', 'selesai'])->default('menunggu');
            $table->string('nomor_surat_usulan')->nullable();
            $table->date('tanggal_surat_usulan')->nullable();
            $table->string('file_sk_lama')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kgb_riwayat');
        Schema::dropIfExists('kgb_gaji_pokok');
    }
};