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
        Schema::create('spj_sub_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spj_bidang_id')->constrained('spj_bidangs')->cascadeOnDelete();
            $table->string('nama_sub_kegiatan');
            $table->string('file_kak')->nullable(); // Disimpan path/nama filenya
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spj_sub_kegiatans');
    }
};
