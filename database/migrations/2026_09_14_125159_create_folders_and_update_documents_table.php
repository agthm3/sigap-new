<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel folders
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('folders')->cascadeOnDelete();
            $table->string('name');
            $table->string('icon')->default('folder'); // nama heroicon / feather icon
            $table->string('color')->default('#B91C1C'); // default maroon
            $table->string('classification_code')->nullable(); // contoh: 900
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Update tabel documents
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('folder_id')->nullable()->after('id')->constrained('folders')->nullOnDelete();
            $table->string('physical_rack')->nullable()->after('description');
            $table->string('physical_row')->nullable()->after('physical_rack');
        });

        // 3. Amankan data lama: set seluruh dokumen yang sudah ada menjadi 'public'
        DB::table('documents')->whereNull('sensitivity')->orWhere('sensitivity', '')->update([
            'sensitivity' => 'public',
        ]);
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn(['folder_id', 'physical_rack', 'physical_row']);
        });

        Schema::dropIfExists('folders');
    }
};