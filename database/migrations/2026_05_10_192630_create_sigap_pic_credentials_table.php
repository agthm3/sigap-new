<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sigap_pic_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained('sigap_pic_systems')->cascadeOnDelete();

            $table->string('nama_akun');
            $table->string('username')->nullable();
            $table->longText('password_encrypted')->nullable(); // akan dienkripsi di model
            $table->string('email')->nullable();
            $table->string('url_login')->nullable();
            $table->string('access_level')->nullable();
            $table->boolean('is_sensitive')->default(true);
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigap_pic_credentials');
    }
};