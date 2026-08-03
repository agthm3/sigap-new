<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magang_peserta', function (Blueprint $table) {
            $table->integer('typing_wpm')->default(0)->after('status');
            $table->timestamp('typing_passed_at')->nullable()->after('typing_wpm');
            $table->string('file_laporan_pdf')->nullable()->after('typing_passed_at');
        });
    }

    public function down(): void
    {
        Schema::table('magang_peserta', function (Blueprint $table) {
            $table->dropColumn(['typing_wpm', 'typing_passed_at', 'file_laporan_pdf']);
        });
    }
};