<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inkubatorma_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inkubatorma_id')
                ->constrained('inkubatormas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->enum('actor_role', [
                'admin',
                'verifikator',
                'user',
            ])->nullable();

            $table->enum('record_type', [
                'sesi_konsultasi',
                'upload_revisi',
                'review_revisi',
                'konfirmasi_selesai',
            ])->default('catatan_umum');

            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->longText('revision_note')->nullable();

            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_mime', 100)->nullable();

            $table->timestamps();

            $table->index(['inkubatorma_id', 'record_type']);
            $table->index(['inkubatorma_id', 'created_at']);
            $table->index('actor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inkubatorma_records');
    }
};