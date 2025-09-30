<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('format_templates', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('category');             // Surat, Nota Dinas, Laporan, Kop Surat, Stempel/TTD, ...
            $t->text('description')->nullable();
            $t->string('lang', 5)->default('id');
            $t->enum('orientation', ['portrait','landscape'])->default('portrait');
            $t->enum('file_type', ['DOCX','PDF','PNG','PPTX','XLSX','SVG']);
            $t->string('file_path');            // storage path
            $t->string('original_name');        // nama file asli
            $t->unsignedBigInteger('size')->default(0);
            $t->enum('privacy', ['public','private'])->default('public');
            $t->string('access_code_hash')->nullable(); // bcrypt hash (untuk privacy=private)
            $t->json('tags')->nullable();

            $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $t->timestamps();
            $t->index(['category','file_type','privacy']);
            $t->index(['title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('format_templates');
    }
};
