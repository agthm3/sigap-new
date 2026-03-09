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
        Schema::create('pegawai_kompetensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('nama_sertifikat');
            $table->string('bidang_sertifikat')->nullable();
            $table->year('tahun_sertifikat')->nullable();

            $table->string('file_path')->nullable(); // upload file
            $table->string('file_name')->nullable();
            $table->string('file_mime')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_kompetensis');
    }
};
