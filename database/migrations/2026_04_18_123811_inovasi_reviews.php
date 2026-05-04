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
        Schema::create('inovasi_reviews', function (Blueprint $table) {
            $table->id();

            // relasi utama
            $table->foreignId('inovasi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();

            // ===== PENILAIAN (SCORING) =====
            $table->integer('nilai_rancang_bangun')->nullable();
            $table->integer('nilai_manfaat')->nullable();
            $table->integer('nilai_kebaruan')->nullable();
            $table->integer('nilai_dampak')->nullable();
            $table->integer('nilai_evidence')->nullable();

            // ===== CATATAN DETAIL =====
            $table->text('catatan_rancang_bangun')->nullable();
            $table->text('catatan_manfaat')->nullable();
            $table->text('catatan_evidence')->nullable();
            $table->text('catatan_umum')->nullable();

            // ===== HASIL AKHIR =====
            $table->enum('rekomendasi', ['Layak', 'Revisi', 'Tidak Layak'])->nullable();

            // ===== STATUS REVIEW =====
            $table->enum('status', ['draft', 'final'])->default('draft');

            // ===== WAKTU =====
            $table->timestamp('reviewed_at')->nullable();

            $table->boolean('is_locked')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
