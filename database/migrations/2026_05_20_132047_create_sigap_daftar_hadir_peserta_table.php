<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
    Schema::create('sigap_daftar_hadir_peserta', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kegiatan_id')->constrained('sigap_daftar_hadir_kegiatan')->cascadeOnDelete();
        $table->string('nama');
        $table->string('instansi');
        $table->enum('gender', ['L', 'P']);
        $table->string('no_hp', 30);
        $table->string('email')->nullable();
        $table->string('ttd_path')->nullable();
        $table->unsignedInteger('urutan_absen')->default(1);
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();

        $table->index(['kegiatan_id', 'urutan_absen']);
        $table->index(['kegiatan_id', 'nama']);
        $table->index(['kegiatan_id', 'no_hp']);
    });
}

public function down(): void
{
    Schema::dropIfExists('sigap_daftar_hadir_peserta');
}
};