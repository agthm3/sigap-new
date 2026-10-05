<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sigap_skps', function (Blueprint $table) {
            $table->text('judul_kegiatan')->change();
        });
    }

    public function down(): void
    {
        Schema::table('sigap_skps', function (Blueprint $table) {
            $table->string('judul_kegiatan', 191)->change();
        });
    }
};