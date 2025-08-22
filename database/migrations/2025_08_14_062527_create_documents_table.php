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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();            // BRIDA/SK/123/2025
            $table->string('title');
            $table->string('alias')->unique();               // SK-12-2025 (unik)
            $table->unsignedSmallInteger('year')->index();
            $table->string('category')->index();             // SK, Laporan, Formulir, Dokumen Privasi
            $table->string('stakeholder')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->enum('sensitivity', ['public','private'])->default('public')->index();
            $table->foreignId('related_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('version')->default('v1.0');
            $table->date('doc_date')->nullable();
            $table->string('file_path');                     // path file di storage
            $table->string('thumb_path')->nullable();        // thumbnail optional
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
