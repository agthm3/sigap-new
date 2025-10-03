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
        Schema::create('evidence_template_params', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('evidence_templates')->cascadeOnDelete();
            $table->text('label');
            $table->unsignedInteger('weight')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            // $table->unique(['template_id','label']);
            $table->index(['template_id','sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_template_params');
    }
};
