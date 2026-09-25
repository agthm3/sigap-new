<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notulensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Pembuat notula

            // Opsi Integrasi SIGAP DAFTAR HADIR (Boleh null jika murni manual)
            $table->unsignedBigInteger('daftar_hadir_kegiatan_id')->nullable();

            // 1. BAGIAN SURAT UNDANGAN / PENGANTAR
            $table->string('nomor_surat')->nullable(); // contoh: 070/825/BPPD/VII/2026
            $table->string('lampiran_surat')->nullable();
            $table->string('perihal_surat')->nullable(); // contoh: NOTULA DAN DOKUMENTASI RAPAT KOORDINASI
            $table->string('tujuan_surat')->nullable(); // contoh: Pejabat Eselon dan Staf BRIDA Kota Makassar
            $table->date('tanggal_surat')->nullable();
            $table->text('isi_pembuka_surat')->nullable();

            // Data Pelaksanaan (Dapat diisi dari Daftar Hadir, tapi tetap editable)
            $table->string('judul_acara'); // Nama Rapat
            $table->string('hari_tanggal'); // contoh: Senin, 27 Juli 2026
            $table->string('waktu'); // contoh: 10.00 WITA - Selesai
            $table->string('tempat'); // contoh: Ruang Rapat BRIDA Kota Makassar

            // Penandatangan Undangan (Pimpinan / Kepala Dinas)
            $table->string('pimpinan_nama')->nullable(); // H. ANDI RAMA, S.Sos., M.Si.
            $table->string('pimpinan_jabatan')->nullable(); // KEPALA BADAN RISET DAN INOVASI DAERAH
            $table->string('pimpinan_pangkat')->nullable(); // Pembina Utama Muda
            $table->string('pimpinan_nip')->nullable();
            $table->string('pimpinan_ttd_image')->nullable(); // path stempel & ttd

            // 2. BAGIAN LEMBAR NOTULA RAPAT
            $table->string('pimpinan_rapat')->nullable();
            $table->text('peserta_ringkas')->nullable(); // contoh teks ringkas peserta
            $table->string('notulis_nama')->nullable();
            $table->string('notulis_ttd_image')->nullable();
            $table->longText('isi_pelaksana_kegiatan')->nullable(); // Poin 1, 2, 3 jalannya rapat

            // 3. BAGIAN DOKUMENTASI (FOTO)
            $table->json('dokumentasi_foto')->nullable(); // Array path gambar ['notula/foto1.jpg', ...]

            // Status Laporan
            $table->enum('status', ['draft', 'proses', 'selesai'])->default('draft');
            $table->timestamps();
        });

        // Tabel untuk Peserta Daftar Hadir (Manual / Copy dari kegiatan lain)
        Schema::create('notulensi_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notulensi_id')->constrained('notulensis')->cascadeOnDelete();
            $table->string('nama');
            $table->string('instansi')->nullable();
            $table->string('nip_nohp')->nullable();
            $table->string('paraf_image')->nullable(); // URL / base64 tanda tangan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notulensi_pesertas');
        Schema::dropIfExists('notulensis');
    }
};