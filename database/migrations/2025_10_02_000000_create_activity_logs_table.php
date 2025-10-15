<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
3
return new class extends Migration {
    public function up(): void {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // siapa
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();     // cache nama saat itu
            $table->string('user_role')->nullable();     // cache role utama saat itu (admin/employee/..)

            // konteks modul & objek
            $table->string('module', 30)->index();       // dokumen/pegawai/inovasi/riset/other
            $table->string('action', 30)->index();       // view/download/create/update/delete/access_denied
            $table->string('object_type')->nullable();   // App\Models\Document, App\Models\UserFile, dll
            $table->unsignedBigInteger('object_id')->nullable()->index();
            $table->string('object_title')->nullable();  // snapshot judul/alias saat itu (biar tetap terbaca kalau judul berubah)
            $table->string('object_alias')->nullable();

            // atribut tambahan
            $table->string('sensitivity', 20)->nullable(); // public/private (snapshot)
            $table->boolean('success')->default(true);
            $table->string('reason', 255)->nullable();     // alasan akses (kalau diisi)
            
            // jejak teknis
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
            $table->index(['module','object_type','object_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('activity_logs');
    }
};
