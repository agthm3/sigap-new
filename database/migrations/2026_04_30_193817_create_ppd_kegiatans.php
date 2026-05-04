<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ppd_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->enum('kategori', ['bimtek', 'koordinasi']);
            $table->string('hari_tanggal');
            $table->string('tempat');
            $table->unsignedTinyInteger('jumlah_lembar')->default(1);
            $table->enum('status', ['draft', 'proses', 'selesai'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppd_kegiatans');
    }
};