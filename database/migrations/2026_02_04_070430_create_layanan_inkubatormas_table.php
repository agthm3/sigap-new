<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_inkubatorma', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            $table->string('nama');                 // contoh: "Pembuatan Video Inovasi"
            $table->string('kode', 10)->unique();   // contoh: "PVI" (unique)
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_inkubatorma');
    }
};
