<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('researches', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedSmallInteger('year');
            $table->enum('type', ['internal','kolaborasi','eksternal'])->nullable();

            $table->text('abstract');
            $table->string('method', 1000)->nullable();

            $table->json('authors');        // [{name, affiliation, role, orcid}]
            $table->json('corresponding')->nullable(); // {name,email,phone}
            $table->json('tags')->nullable();
            $table->json('stakeholders')->nullable();

            $table->string('doi')->nullable();
            $table->string('ojs_url')->nullable();
            $table->string('funding')->nullable();
            $table->string('ethics')->nullable();

            $table->string('version', 20)->nullable();
            $table->string('release_note')->nullable();

            $table->enum('access', ['Public','Restricted'])->default('Public');
            $table->string('access_reason', 500)->nullable();
            $table->string('license', 60)->nullable();

            $table->string('file_path'); // storage path
            $table->string('file_name');
            $table->string('file_size', 30)->nullable();
            $table->string('thumbnail_path')->nullable();

            $table->json('datasets')->nullable(); // [{label, path, original_name, size}]

            $table->unsignedInteger('stats_views')->default(0);
            $table->unsignedInteger('stats_downloads')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('researches');
    }
};
