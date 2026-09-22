<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kgb_gaji_pokok', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_pegawai', ['pns', 'pppk'])->index();
            $table->string('golongan', 20)->index();
            $table->unsignedTinyInteger('masa_kerja')->index();
            $table->unsignedBigInteger('nominal');
            $table->string('regulasi', 100)->default('PP 5/2024');
            $table->timestamps();

            $table->unique(['jenis_pegawai', 'golongan', 'masa_kerja', 'regulasi'], 'kgb_gaji_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kgb_gaji_pokok');
    }
};