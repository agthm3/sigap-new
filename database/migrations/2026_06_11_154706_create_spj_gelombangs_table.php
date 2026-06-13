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
        Schema::create('spj_gelombangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spj_kegiatan_id')->constrained('spj_kegiatans')->cascadeOnDelete();
            $table->string('nama_gelombang'); // cth: "Gelombang 1", "Angkatan 1"
            
            // File inputan 10 jenis dokumen
            $table->string('file_sk_narasumber')->nullable();
            $table->string('file_sk_moderator')->nullable();
            $table->string('file_sp_narasumber')->nullable();
            $table->string('file_sp_moderator')->nullable();
            $table->string('file_sp_panitia')->nullable();
            $table->string('file_surat_undangan')->nullable();
            $table->string('file_daftar_hadir')->nullable();
            $table->string('file_notulensi')->nullable();
            $table->string('file_dokumentasi')->nullable();
            $table->string('file_materi')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spj_gelombangs');
    }
};
