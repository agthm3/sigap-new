<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inkubatorma_logs', function (Blueprint $table) {
            $table->id();

            // Relasi ke pengajuan utama
            $table->foreignId('inkubatorma_id')
                ->constrained('inkubatormas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Aksi timeline (enum sesuai desain)
            $table->enum('aksi', [
                'SUBMIT',
                'APPROVE',
                'SET_SCHEDULE',
                'RESCHEDULE',
                'REJECT',
                'CLOSE',
            ]);

            // Status sebelum & sesudah (enum supaya konsisten)
            $table->enum('status_dari', [
                'Menunggu',
                'Akan Dijadwalkan',
                'Terjadwal',
                'Sesi Konsultasi',
                'Dijadwalkan Ulang',
                'Ditolak',
                'Selesai',
            ])->nullable();

            $table->enum('status_ke', [
                'Menunggu',
                'Akan Dijadwalkan',
                'Terjadwal',
                'Sesi Konsultasi',
                'Dijadwalkan Ulang',
                'Ditolak',
                'Selesai',
            ])->nullable();

            // Catatan tambahan untuk log (misalnya alasan reschedule/reject)
            $table->text('catatan')->nullable();

            // Aktor (pegawai) yang melakukan aksi
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Waktu kejadian log (pakai timestamp biar bisa custom)
            $table->timestamp('created_at')->useCurrent();

            // Index untuk query timeline cepat
            $table->index(['inkubatorma_id', 'created_at']);
            $table->index(['aksi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inkubatorma_logs');
    }
};
