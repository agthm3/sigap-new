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
        Schema::create('inovasi_review_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inovasi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();

            $table->string('field'); // contoh: judul, tujuan, dll

            $table->enum('status', ['accept', 'revisi', 'tolak'])->default('accept');

            $table->text('comment')->nullable();

            $table->integer('point')->default(0);

            $table->timestamps();

            // 1 reviewer hanya bisa review 1 field per inovasi
            $table->unique(['inovasi_id', 'reviewer_id', 'field']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inovasi_review_items');
    }
};
