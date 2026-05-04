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
        Schema::create('evidence_review_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inovasi_id')->constrained('inovasis')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('no');          // 1–20
            $table->enum('status', ['accept','revisi','tolak']);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['inovasi_id','reviewer_id','no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_review_items');
    }
};
