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
     Schema::create('sigap_agenda_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sigap_agenda_id')
                ->constrained('sigap_agendas')
                ->cascadeOnDelete();
            $table->integer('order_no')->nullable();
            $table->enum('mode', ['kepala', 'menugaskan', 'custom'])->default('kepala');
            $table->text('assignees')->nullable();
            $table->text('description');
            $table->string('time_text');
            $table->string('place');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sigap_agenda_items');
    }
};
