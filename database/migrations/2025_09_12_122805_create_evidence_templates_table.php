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
       Schema::create('evidence_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('no')->unique();             // 1..20
            $table->string('indikator', 191);
            $table->text('keterangan')->nullable();
            $table->string('jenis_file', 100)->nullable();           // "Dokumen PDF" / "Foto/Gambar" / "Upload Video .mp4"
            $table->text('hint')->nullable();
            $table->timestamps();

            $table->index(['no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_templates');
    }
};
