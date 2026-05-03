<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InovasiReviewTemplateSeeder extends Seeder
{
    public function run()
    {
        DB::table('inovasi_review_templates')->insert([

            // =========================
            // IDENTITAS DASAR
            // =========================
            ['field' => 'judul', 'label' => 'Judul Inovasi', 'point' => 1],
            ['field' => 'opd_unit', 'label' => 'OPD / Unit', 'point' => 1],
            ['field' => 'tahap_inovasi', 'label' => 'Tahapan Inovasi', 'point' => 1],
            ['field' => 'inisiator_daerah', 'label' => 'Inisiator Daerah', 'point' => 1],
            ['field' => 'inisiator_nama', 'label' => 'Nama Inisiator', 'point' => 1],
            ['field' => 'koordinat', 'label' => 'Koordinat', 'point' => 1],

            // =========================
            // KLASIFIKASI & KEBIJAKAN
            // =========================
            ['field' => 'klasifikasi', 'label' => 'Klasifikasi Inovasi', 'point' => 1],
            ['field' => 'jenis_inovasi', 'label' => 'Jenis Inovasi', 'point' => 1],
            ['field' => 'bentuk_inovasi_daerah', 'label' => 'Bentuk Inovasi Daerah', 'point' => 2],

            ['field' => 'asta_cipta', 'label' => 'Asta Cipta', 'point' => 2],
            ['field' => 'program_prioritas', 'label' => 'Program Prioritas Walikota', 'point' => 2],
            ['field' => 'misi_walikota', 'label' => 'Misi Walikota', 'point' => 2],
            ['field' => 'urusan_pemerintah', 'label' => 'Urusan Pemerintah', 'point' => 2],

            // =========================
            // WAKTU & STATUS
            // =========================
            ['field' => 'waktu_uji_coba', 'label' => 'Waktu Uji Coba', 'point' => 1],
            ['field' => 'waktu_penerapan', 'label' => 'Waktu Penerapan', 'point' => 1],
            ['field' => 'perkembangan_inovasi', 'label' => 'Perkembangan Inovasi', 'point' => 1],

            // =========================
            // SUBSTANSI (INTI PENILAIAN)
            // =========================
            ['field' => 'rancang_bangun', 'label' => 'Rancang Bangun', 'point' => 5],
            ['field' => 'tujuan', 'label' => 'Tujuan Inovasi', 'point' => 4],
            ['field' => 'manfaat', 'label' => 'Manfaat Inovasi', 'point' => 4],
            ['field' => 'hasil_inovasi', 'label' => 'Hasil Inovasi', 'point' => 5],

            // =========================
            // REFERENSI
            // =========================
            ['field' => 'videos', 'label' => 'Referensi Penelitian / Video', 'point' => 3],

            // =========================
            // FILE / DOKUMEN
            // =========================
            ['field' => 'anggaran', 'label' => 'Dokumen Anggaran', 'point' => 3],
            ['field' => 'profil_bisnis', 'label' => 'Profil Bisnis', 'point' => 2],
            ['field' => 'haki', 'label' => 'Dokumen HAKI', 'point' => 2],
            ['field' => 'penghargaan', 'label' => 'Penghargaan', 'point' => 2],

        ]);
    }
}