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
        Schema::create('inovasi_review_templates', function (Blueprint $table) {
            $table->id();

            $table->string('field'); // nama field: judul, tujuan, dll
            $table->string('label'); // label tampilan
            $table->integer('point'); // bobot nilai

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inovasi_review_templates');
    }
};
