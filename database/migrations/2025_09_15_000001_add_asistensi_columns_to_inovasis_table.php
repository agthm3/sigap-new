// database/migrations/2025_09_15_000001_add_asistensi_columns_to_inovasis_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inovasis', function (Blueprint $table) {
            if (!Schema::hasColumn('inovasis','asistensi_status')) {
                $table->string('asistensi_status')->default('Menunggu Verifikasi')->after('perkembangan_inovasi');
            }
            if (!Schema::hasColumn('inovasis','asistensi_note')) {
                $table->text('asistensi_note')->nullable()->after('asistensi_status');
            }
            if (!Schema::hasColumn('inovasis','asistensi_by')) {
                $table->foreignId('asistensi_by')->nullable()->after('asistensi_note')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('inovasis','asistensi_at')) {
                $table->timestamp('asistensi_at')->nullable()->after('asistensi_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inovasis', function (Blueprint $table) {
            if (Schema::hasColumn('inovasis','asistensi_at')) $table->dropColumn('asistensi_at');
            if (Schema::hasColumn('inovasis','asistensi_by')) $table->dropConstrainedForeignId('asistensi_by');
            if (Schema::hasColumn('inovasis','asistensi_note')) $table->dropColumn('asistensi_note');
            if (Schema::hasColumn('inovasis','asistensi_status')) $table->dropColumn('asistensi_status');
        });
    }
};
