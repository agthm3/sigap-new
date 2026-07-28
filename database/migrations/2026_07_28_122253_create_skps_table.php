<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sigap_skps', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('agenda_id')->nullable()->constrained('sigap_agendas')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('judul_kegiatan');
            $table->date('tanggal');
            
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        
        Schema::create('sigap_skp_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skp_id')->constrained('sigap_skps')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sigap_skp_user');
        Schema::dropIfExists('sigap_skps');
    }
};