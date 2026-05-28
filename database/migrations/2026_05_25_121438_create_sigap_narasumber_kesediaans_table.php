<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sigap_narasumber_kesediaans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('kegiatan_id')->constrained('sigap_daftar_hadir_kegiatan')->onDelete('cascade');
            
            // Biodata & Kesediaan
            $table->string('nama_lengkap');
            $table->string('nip')->nullable();
            $table->string('tempat_tanggal_lahir')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('instansi_unit_kerja')->nullable();
            $table->string('agama')->nullable();
            $table->string('alamat_kantor')->nullable();
            $table->string('alamat_rumah')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('status_keluarga')->nullable();
            $table->string('hobby')->nullable();
            $table->string('materi')->nullable();
            $table->string('npwp')->nullable();
            $table->string('no_rekening')->nullable();
            
            // TTD
            $table->string('ttd_path')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('tempat_ttd')->default('Makassar');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigap_narasumber_kesediaans');
    }
};