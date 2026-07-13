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
        Schema::create('iga_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('role', 50)->nullable()->default('opd'); 
            $table->string('daerah')->nullable(); 
            $table->string('opd'); 
            $table->string('username'); 
            $table->string('password_raw'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iga_accounts');
    }
};