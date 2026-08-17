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
        Schema::table('sigap_skps', function (Blueprint $table) {
            $table->string('kategori')->default('TUPOKSI')->after('judul_kegiatan');
            $table->enum('tipe_evidence', ['foto', 'pdf'])->default('foto')->after('kategori');
            $table->text('deskripsi')->nullable()->after('tanggal');
            $table->string('file_pdf_path')->nullable()->after('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sigap_skps', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'tipe_evidence', 'deskripsi', 'file_pdf_path']);
        });
    }
};