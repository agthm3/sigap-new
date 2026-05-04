<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ppd_lembar_laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppd_kegiatan_id')->constrained('ppd_kegiatans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('lembar_ke');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['ppd_kegiatan_id', 'user_id', 'lembar_ke'], 'ppd_lembar_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppd_lembar_laporans');
    }
};