<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Batch Magang
        Schema::create('magang_batches', function (Blueprint $table) {
            $table->id();
            $table->string('nama_batch');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('kuota')->default(0);
            $table->enum('status', ['mendatang', 'aktif', 'selesai'])->default('mendatang');
            $table->timestamps();
        });

        // 2. Tabel Pivot Peserta Batch Magang
        Schema::create('magang_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_batch_id')->constrained('magang_batches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('instansi_asal')->nullable();
            $table->string('jurusan')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('diterima');
            $table->timestamps();

            $table->unique(['magang_batch_id', 'user_id']);
        });

        // 3. Tabel Logbook Peserta Magang
        Schema::create('magang_logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_batch_id')->constrained('magang_batches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('kegiatan');
            $table->string('file_lampiran')->nullable();
            $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_revisi')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magang_logbooks');
        Schema::dropIfExists('magang_peserta');
        Schema::dropIfExists('magang_batches');
    }
};