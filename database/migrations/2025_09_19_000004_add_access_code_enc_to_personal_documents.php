<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('personal_documents', function (Blueprint $t) {
      $t->text('access_code_enc')->nullable()->after('access_code_hash'); // simpan terenkripsi
    });
  }
  public function down(): void {
    Schema::table('personal_documents', function (Blueprint $t) {
      $t->dropColumn('access_code_enc');
    });
  }
};
