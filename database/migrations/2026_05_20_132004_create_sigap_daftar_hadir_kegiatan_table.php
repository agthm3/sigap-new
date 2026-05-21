<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sigap_daftar_hadir_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nama_kegiatan');
            $table->string('hari_tanggal');
            $table->string('tempat');
            $table->string('waktu');
            $table->enum('status', ['draft', 'proses', 'selesai'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigap_daftar_hadir_kegiatan');
    }
};