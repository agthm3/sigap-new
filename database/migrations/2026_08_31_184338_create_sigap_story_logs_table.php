<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sigap_story_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kinerja_id')->nullable(); // Terhubung ke tabel kinerja (opsional)
            $table->foreignId('user_id')->nullable();
            $table->string('title')->nullable();
            $table->string('image_path'); // Path gambar yang disimpan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sigap_story_logs');
    }
};
