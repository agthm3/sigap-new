<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personal_document_logs', function (Blueprint $table) {
            // pakai json kalau MySQL >= 5.7; kalau <5.7 ganti ke text
            $table->json('extra')->nullable()->after('action');
        });
    }

    public function down(): void
    {
        Schema::table('personal_document_logs', function (Blueprint $table) {
            $table->dropColumn('extra');
        });
    }
};
