<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('personal_documents', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete(); // pemilik dokumen
      $t->string('type', 30); // ktp, kk, npwp, bpjs, dll
      $t->string('title');    // judul tampil
      $t->string('path');     // storage path
      $t->string('mime', 100)->nullable();
      $t->unsignedBigInteger('size')->default(0);
      $t->enum('status', ['pending','verified','rejected'])->default('pending');
      $t->timestamp('verified_at')->nullable();
      $t->text('notes')->nullable(); // catatan verifikator
      $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
      $t->timestamps();
    });
  }
  public function down(): void {
    Schema::dropIfExists('personal_documents');
  }
};
