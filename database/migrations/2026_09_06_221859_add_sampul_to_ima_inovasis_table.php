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
        Schema::table('ima_inovasis', function (Blueprint $table) {
            $table->string('sampul_file')->nullable()->after('hasil_inovasi');
        });
    }

    public function down(): void
    {
        Schema::table('ima_inovasis', function (Blueprint $table) {
            $table->dropColumn('sampul_file');
        });
    }
};
