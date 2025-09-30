<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('personal_document_logs', function (Blueprint $t) {
      $t->id();
      $t->foreignId('personal_document_id')->constrained()->cascadeOnDelete();
      $t->foreignId('acted_by')->constrained('users')->cascadeOnDelete(); // siapa akses
      $t->string('action', 20); // view/download
      $t->string('ip', 45)->nullable();
      $t->text('user_agent')->nullable();
      // $table->json('extra')->nullable()->after('action');
      $t->timestamps();
    });
  }
  public function down(): void {
    Schema::dropIfExists('personal_document_logs');
  }
};
