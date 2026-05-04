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
            Schema::create('sigap_absensis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();

                $table->date('absen_date');
                $table->time('absen_time');

                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->string('location_text')->nullable();

                $table->string('photo_path');
                $table->string('keterangan')->default('HADIR');

                $table->unsignedInteger('late_minutes')->default(0);
                $table->timestamps();
                $table->boolean('is_outside_radius')->default(false);
                $table->decimal('distance_meter', 10, 2)->nullable();
                $table->unique(['user_id', 'absen_date']);
                $table->index(['absen_date', 'absen_time']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
