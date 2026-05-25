<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Master data pejabat — reusable lintas kegiatan
        Schema::create('sigap_daftar_hadir_pejabat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('jabatan');
            $table->string('pangkat')->nullable();
            $table->string('golongan', 10)->nullable();
            $table->string('nip', 30)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('nama_lengkap');
        });

        // Penandatangan per kegiatan — menyimpan snapshot data pejabat + TTD
        Schema::create('sigap_daftar_hadir_penandatangan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Relasi ke kegiatan
            $table->foreignId('kegiatan_id')
                  ->constrained('sigap_daftar_hadir_kegiatan')
                  ->cascadeOnDelete();

            // Snapshot data pejabat saat kegiatan dibuat
            // (disimpan terpisah agar perubahan master tidak merusak histori)
            $table->foreignId('pejabat_id')
                  ->nullable()
                  ->constrained('sigap_daftar_hadir_pejabat')
                  ->nullOnDelete();

            $table->string('nama_lengkap');
            $table->string('jabatan');
            $table->string('pangkat')->nullable();
            $table->string('golongan', 10)->nullable();
            $table->string('nip', 30)->nullable();

            // Tempat/tanggal TTD (bisa berbeda dengan tempat/tanggal kegiatan)
            $table->string('tempat_ttd')->nullable();
            $table->string('tanggal_ttd')->nullable();

            // File TTD — diisi setelah pejabat scan QR khusus
            $table->string('ttd_path')->nullable();
            $table->timestamp('signed_at')->nullable();

            $table->timestamps();

            $table->unique('kegiatan_id'); // 1 kegiatan = 1 penandatangan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigap_daftar_hadir_penandatangan');
        Schema::dropIfExists('sigap_daftar_hadir_pejabat');
    }
};