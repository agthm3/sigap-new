<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigap_skp_kumpulans', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 191);
            $table->unsignedBigInteger('user_id');
            $table->string('kategori', 100);
            $table->string('bulan_tahun', 7);
            $table->string('judul_kumpulan', 255);
            $table->longText('skp_ids');
            $table->longText('ppd_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigap_skp_kumpulans');
    }
};