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
        Schema::create('sertifikat_pesertas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kegiatan_id')
                  ->constrained('sertifikat_kegiatans')
                  ->cascadeOnDelete();

            $table->string('nomor_sertifikat')->unique();
            $table->string('nama_penerima');
            $table->string('instansi')->nullable();
            $table->text('keterangan')->nullable();

            $table->enum('status',['Aktif','Nonaktif'])->default('Aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat_pesertas');
    }
};
