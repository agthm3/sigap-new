<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sigap_pic_systems', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sistem');
            $table->string('slug')->unique();
            $table->string('kategori')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('url')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->enum('status', ['aktif', 'maintenance', 'nonaktif'])->default('aktif');
            $table->string('level_kritis')->nullable(); // rendah/sedang/tinggi
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('youtube_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigap_pic_systems');
    }
};