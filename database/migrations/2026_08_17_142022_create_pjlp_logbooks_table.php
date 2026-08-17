<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pjlp_logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pjlp_periode_id')->constrained('pjlp_periodes')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('hari'); // Senin, Selasa, dst.
            $table->text('deskripsi_pekerjaan')->nullable();
            $table->string('foto_evidence')->nullable();
            $table->enum('status', ['belum_diisi', 'draft', 'diajukan', 'terverifikasi', 'ditolak'])->default('belum_diisi');
            $table->text('catatan_verifikator')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['pjlp_periode_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pjlp_logbooks');
    }
};