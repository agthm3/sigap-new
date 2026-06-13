<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('spj_gelombangs', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('nama_gelombang');
            $table->string('waktu')->nullable()->after('tanggal'); // cth: "08:00 - Selesai"
            $table->string('tempat')->nullable()->after('waktu');
        });
    }

    public function down()
    {
        Schema::table('spj_gelombangs', function (Blueprint $table) {
            $table->dropColumn(['tanggal', 'waktu', 'tempat']);
        });
    }
};