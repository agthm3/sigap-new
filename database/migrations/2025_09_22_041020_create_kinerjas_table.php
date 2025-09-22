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
        Schema::create('kinerjas', function (Blueprint $table) {
            $table->id();
            $table->string('category');           // KINERJA A / Rapat / dst
            $table->string('rhk')->nullable();    // H / RHK-1 / dst
            $table->string('title');              // Judul / Nama kegiatan
            $table->text('description')->nullable();
            $table->date('activity_date');        // Tanggal kegiatan (input)
            $table->string('file_path');          // path di storage/app/public
            $table->string('file_mime')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('thumb_path')->nullable(); // boleh null → blade fallback dummy
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kinerjas');
    }
};
