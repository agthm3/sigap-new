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
        Schema::table('ima_dropdowns', function (Blueprint $table) {
            if (!Schema::hasColumn('ima_dropdowns', 'targets')) {
                $table->longText('targets')->nullable()->after('deskripsi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ima_dropdowns', function (Blueprint $table) {
            if (Schema::hasColumn('ima_dropdowns', 'targets')) {
                $table->dropColumn('targets');
            }
        });
    }
};