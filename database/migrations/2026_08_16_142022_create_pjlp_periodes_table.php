<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pjlp_periodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('bulan_tahun', 7); // Format: '2026-08'
            $table->string('file_daftar_gaji')->nullable(); // Path PDF daftar gaji
            $table->string('status_laporan')->default('draft'); // draft, lengkap, terverifikasi
            $table->timestamps();

            $table->unique(['user_id', 'bulan_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pjlp_periodes');
    }
};