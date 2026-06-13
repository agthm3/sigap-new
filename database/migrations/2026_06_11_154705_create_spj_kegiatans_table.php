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
        Schema::create('spj_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spj_sub_kegiatan_id')->constrained('spj_sub_kegiatans')->cascadeOnDelete();
            $table->string('nama_kegiatan');
            $table->string('file_sk_panpel')->nullable();
            $table->string('file_sk_tenaga_ahli')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spj_kegiatans');
    }
};
