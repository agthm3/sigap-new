<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('format_access_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('format_template_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 20)->default('download'); // download/view
            $t->boolean('success')->default(true);
            $t->string('ip', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('format_access_logs');
    }
};
