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
    Schema::create('inovasi_pedoman_metas', function (Blueprint $table) {
        $table->id();
        $table->string('field_key')->unique(); // judul, rancang_bangun, dll
        $table->text('deskripsi')->nullable();
        $table->string('video_url')->nullable(); // link YouTube tutorial
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inovasi_pedoman_metas');
    }
};
