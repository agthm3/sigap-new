<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE inkubatorma_logs
            MODIFY aksi ENUM(
                'SUBMIT',
                'APPROVE',
                'SET_SCHEDULE',
                'SESSION_STARTED',
                'RESCHEDULE',
                'REJECT',
                'CLOSE'
            )
        ");

        DB::statement("
            ALTER TABLE inkubatorma_logs
            MODIFY status_dari ENUM(
                'Menunggu',
                'Akan Dijadwalkan',
                'Terjadwal',
                'Dijadwalkan Ulang',
                'Sesi Konsultasi',
                'Ditolak',
                'Selesai'
            ) DEFAULT 'Menunggu'
        ");

        DB::statement("
            ALTER TABLE inkubatorma_logs
            MODIFY status_ke ENUM(
                'Menunggu',
                'Akan Dijadwalkan',
                'Terjadwal',
                'Dijadwalkan Ulang',
                'Sesi Konsultasi',
                'Ditolak',
                'Selesai'
            ) DEFAULT 'Menunggu'
        ");
    }

    public function down(): void {}
};