<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sigap_daftar_hadir_kegiatan', function (Blueprint $table) {
            $table->string('undangan_path')->nullable()->after('status');
            $table->boolean('buat_sertifikat')->default(0)->after('undangan_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sigap_kegiatan', function (Blueprint $table) {
            //
        });
    }
};
