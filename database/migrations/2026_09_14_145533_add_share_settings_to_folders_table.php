<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->string('share_token', 64)->nullable()->unique()->after('visibility');
            $table->string('share_password')->nullable()->after('share_token');
            $table->timestamp('share_expires_at')->nullable()->after('share_password');
        });
    }

    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dropColumn(['share_token', 'share_password', 'share_expires_at']);
        });
    }
};