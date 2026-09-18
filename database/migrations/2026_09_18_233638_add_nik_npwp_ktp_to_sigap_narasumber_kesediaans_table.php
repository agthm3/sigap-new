<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('sigap_narasumber_kesediaans', function (Blueprint $table) {
        $table->string('nik', 16)->nullable()->after('nip');
        // Jika kolom 'npwp' belum ada sebelumnya, tambahkan baris berikut:
        if (!Schema::hasColumn('sigap_narasumber_kesediaans', 'npwp')) {
            $table->string('npwp', 25)->nullable()->after('nik');
        }
        $table->string('ktp_path')->after('npwp');
    });
}

public function down(): void
{
    Schema::table('sigap_narasumber_kesediaans', function (Blueprint $table) {
        $table->dropColumn(['nik', 'ktp_path']);
        // Drop npwp juga jika baru ditambahkan di sini
    });
}
};
