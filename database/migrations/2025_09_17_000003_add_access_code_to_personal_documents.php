<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('personal_documents', function (Blueprint $t) {
      $t->string('access_code_hash', 255)->nullable()->after('notes');
      $t->string('access_code_hint', 100)->nullable()->after('access_code_hash'); // opsional, contoh: "4 huruf terakhir NIK"
      $t->timestamp('access_code_set_at')->nullable()->after('access_code_hint');
    });
  }

  public function down(): void {
    Schema::table('personal_documents', function (Blueprint $t) {
      $t->dropColumn(['access_code_hash','access_code_hint','access_code_set_at']);
    });
  }
};
