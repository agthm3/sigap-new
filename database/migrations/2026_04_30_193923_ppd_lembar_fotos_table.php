<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ppd_lembar_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppd_lembar_laporan_id')->constrained('ppd_lembar_laporans')->cascadeOnDelete();
            $table->string('foto_path');
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();

            $table->unique(['ppd_lembar_laporan_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppd_lembar_fotos');
    }
};