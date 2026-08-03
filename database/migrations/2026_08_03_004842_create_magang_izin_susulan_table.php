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
        Schema::create('magang_izin_susulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_batch_id')->constrained('magang_batches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->foreignId('given_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Memastikan tidak ada tanggal susulan ganda untuk user dan batch yang sama
            $table->unique(['magang_batch_id', 'user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magang_izin_susulan');
    }
};