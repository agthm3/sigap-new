<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kinerja_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kinerja_id')->index();
            $table->string('path');          // storage path (public disk)
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->boolean('is_image')->default(false);
            $table->boolean('is_primary')->default(false); // untuk cover/thumbnail
            $table->timestamps();

            $table->foreign('kinerja_id')->references('id')->on('kinerjas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinerja_media');
    }
};
