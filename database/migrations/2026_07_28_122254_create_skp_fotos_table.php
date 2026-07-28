<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sigap_skp_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skp_id')->constrained('sigap_skps')->cascadeOnDelete();
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sigap_skp_fotos');
    }
};