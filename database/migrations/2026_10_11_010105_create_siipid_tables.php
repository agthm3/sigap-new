<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah Role Spatie untuk SIIPID jika belum ada
        $guard = 'web';
        $roles = ['verif_siipid', 'inovator_siipid'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => $guard]);
        }

        // 2. Tabel Utama: siipid_prestasis
        Schema::create('siipid_prestasis', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Polymorphic relation to Inovasi or ImaInovasi
            $table->string('innovable_type');
            $table->unsignedBigInteger('innovable_id');
            $table->index(['innovable_type', 'innovable_id']);

            // Data Inovator & Sasaran
            $table->enum('kategori_sasaran', ['asn', 'perangkat_daerah', 'masyarakat', 'dprd', 'kelompok'])->default('asn');
            $table->string('nama_inovator');
            $table->string('nik', 30)->nullable();
            $table->string('nip', 30)->nullable();
            $table->string('jabatan')->nullable();
            $table->string('opd_instansi')->nullable();
            $table->string('no_wa', 25)->nullable();
            $table->string('email', 100)->nullable();

            // Jenis Kepesertaan & Anggota Tim
            $table->enum('jenis_kepesertaan', ['perorangan', 'kelompok'])->default('perorangan');
            $table->json('anggota_tim')->nullable(); // array of [nama, nik_nip, peran]

            // Data Prestasi
            $table->string('nama_prestasi');
            $table->string('ajang_kompetisi');
            $table->unsignedSmallInteger('tahun_perolehan');
            $table->enum('tingkat_prestasi', ['kota', 'provinsi', 'nasional', 'internasional', 'khusus'])->default('kota');
            $table->string('kategori_khusus')->nullable();
            $table->string('peringkat_capaian')->nullable(); // Juara 1, Top 10, Finalis
            $table->string('lembaga_pemberi')->nullable();
            $table->text('deskripsi_prestasi')->nullable();

            // Dokumen Unggahan (Path di disk public)
            $table->string('bukti_prestasi_file'); // Piagam/Sertifikat/SK
            $table->string('bukti_penerapan_file')->nullable();
            $table->string('surat_keaslian_file')->nullable();
            $table->string('surat_kontribusi_tim_file')->nullable();

            // Usulan Bentuk Penghargaan / Insentif
            $table->string('usulan_bentuk_penghargaan')->nullable(); // Piagam, Trofi, Plakat, dll
            $table->string('usulan_bentuk_insentif')->nullable(); // Uang, TPP, Fasilitasi HKI, dll

            // Siklus Review & Status
            $table->enum('status', [
                'draft',
                'diajukan',
                'dalam_review',
                'dikembalikan_perbaikan',
                'ditolak',
                'direkomendasikan',
                'ditetapkan_sk'
            ])->default('draft');

            $table->text('catatan_review_terakhir')->nullable();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            // Penetapan Keputusan Wali Kota
            $table->string('nomor_sk_walikota')->nullable();
            $table->date('tanggal_sk_walikota')->nullable();
            $table->string('file_sk_walikota')->nullable();

            // Visibilitas Publik
            $table->boolean('is_published_public')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Tabel Riwayat Catatan Review (Audit Trail Reviewer)
        Schema::create('siipid_review_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestasi_id')->constrained('siipid_prestasis')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->string('status_sebelumnya', 50)->nullable();
            $table->string('status_baru', 50);
            $table->text('catatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siipid_review_logs');
        Schema::dropIfExists('siipid_prestasis');
    }
};
