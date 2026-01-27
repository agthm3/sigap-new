<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inovasi_referensi_video', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inovasi_id')
                ->constrained('inovasis')
                ->cascadeOnDelete();

            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();
            $table->string('video_url', 500);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inovasi_referensi_video');
    }
};
