<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inovasis', function (Blueprint $table) {
            $table->string('koordinat', 300)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('inovasis', function (Blueprint $table) {
            $table->string('koordinat', 191)->nullable()->change();
        });
    }
};