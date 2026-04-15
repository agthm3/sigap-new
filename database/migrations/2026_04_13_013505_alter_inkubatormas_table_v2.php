<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah layanan_id ke JSON
        DB::statement("ALTER TABLE inkubatormas MODIFY layanan_id JSON NULL");

        // 2. Tambah lampiran (SAFE)
        if (!Schema::hasColumn('inkubatormas', 'lampiran')) {
            DB::statement("
                ALTER TABLE inkubatormas
                ADD COLUMN lampiran JSON NULL
            ");
        }

        // 3. Update ENUM
        DB::statement("
            ALTER TABLE inkubatormas
            MODIFY status ENUM(
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

    public function down(): void
    {
        // optional
    }
};