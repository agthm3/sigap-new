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
        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inovasi_id')->constrained('inovasis')->cascadeOnDelete();

            // mapping ke template/indikator
            $table->foreignId('template_id')->nullable()->constrained('evidence_templates')->nullOnDelete();
            $table->unsignedTinyInteger('no');                       // 1..20
            $table->string('indikator', 191);                        // snapshot judul indikator (stabil)

            // pilihan parameter yang dipilih user (snapshot)
            $table->string('parameter_label', 191)->nullable();
            $table->integer('parameter_weight')->default(0);

            // isian tambahan
            $table->string('jenis_file', 100)->nullable();           // snapshot ekspektasi jenis
            $table->string('deskripsi', 255)->nullable();

            // file upload
            $table->string('file_path', 255)->nullable();
            $table->string('file_name', 191)->nullable();
            $table->string('file_mime', 100)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);

            // optional jika ada link luar
            $table->string('link_url', 255)->nullable();

            // optional status review
            $table->string('review_status', 30)->nullable();         // 'pending','approved','rejected'
            $table->text('review_note')->nullable();

            $table->timestamps();

            $table->unique(['inovasi_id','no']);                     // 1 inovasi 1 row per indikator
            $table->index(['inovasi_id','template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence');
    }
};
