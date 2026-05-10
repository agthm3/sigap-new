<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sigap_pic_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained('sigap_pic_systems')->cascadeOnDelete();

            // nanti bisa dihubungkan ke SIGAP Pegawai
            $table->string('pegawai_nik', 30)->nullable();

            $table->string('nama_pic');
            $table->string('jabatan_pic')->nullable();
            $table->string('bidang')->nullable();
            $table->text('tanggung_jawab')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('urutan')->default(0);
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigap_pic_assignments');
    }
};