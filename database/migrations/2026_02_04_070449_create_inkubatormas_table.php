<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inkubatormas', function (Blueprint $table) {
            $table->id(); 

            $table->string('kode', 30)->unique();
            $table->string('layanan_id', 60);
            $table->string('layanan_lainnya', 255)->nullable();
            $table->string('judul_konsultasi', 191);
            $table->enum('status', [
                'Menunggu',
                'Akan Dijadwalkan',
                'Terjadwal',
                'Dijadwalkan Ulang',
                'Ditolak',
                'Selesai',
            ])->default('Menunggu');

            $table->string('nama_pengaju', 191);
            $table->string('hp_pengaju', 20)->nullable();
            $table->string('opd_unit', 191);
            $table->text('keluhan')->nullable();
            $table->text('poin_asistensi')->nullable();

            $table->date('tanggal_usulan')->nullable();
            $table->time('jam_usulan')->nullable();
            $table->enum('metode_usulan', ['online', 'offline'])->nullable();
            $table->string('target_personil_usulan', 191)->nullable();

            // text
            $table->text('catatan_verifikator')->nullable();
            $table->foreignId('verifikator_employee_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('verifikasi_at')->nullable();

            $table->foreignId('pic_employee_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->date('tanggal_final')->nullable();
            $table->time('jam_final')->nullable();
            $table->enum('metode_final', ['online', 'offline'])->nullable();
            $table->string('lokasi_link_final', 191)->nullable();


            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();

            $table->index('layanan_id', 'inkubatormas_layanan_id_foreign');
            $table->index(['status', 'layanan_id'], 'inkubatormas_status_layanan_id_index');
            $table->index('tanggal_usulan', 'inkubatormas_tanggal_usulan_index');
            $table->index('tanggal_final', 'inkubatormas_tanggal_final_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inkubatormas');
    }
};