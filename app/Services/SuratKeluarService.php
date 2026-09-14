<?php

namespace App\Services;

use App\Models\SuratKeluar;
use Carbon\Carbon;

class SuratKeluarService
{
    /**
     * Konversi angka bulan ke Romawi
     */
    public static function getRomawi(int $month): string
    {
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romawi[$month] ?? 'I';
    }

    /**
     * Daftar Paten Kode Klasifikasi Permendagri 83/2022
     * Dikelompokkan dengan prioritas kode yang sering digunakan di instansi riset/daerah
     */
    public static function getDaftarKlasifikasi(): array
    {
        return [
            '⭐ SERING DIGUNAKAN (POPULER)' => [
                '070'       => '070 - Penelitian, Pengembangan & Inovasi Daerah',
                '005'       => '005 - Undangan Pertemuan / Rapat Dinas',
                '090'       => '090 - Perjalanan Dinas (SPPD / Surat Tugas)',
                '000.2.3'   => '000.2.3 - Pemberitahuan / Edaran / Pengumuman',
                '000.2.4'   => '000.2.4 - Rekomendasi / Keterangan',
                '000.2.5'   => '000.2.5 - Pengantar Berkas / Dokumen',
                '000.2.6'   => '000.2.6 - Berita Acara / Laporan Hasil',
            ],
            '000 - UMUM & KETATAUSAHAAN' => [
                '000'       => '000 - Ketatausahaan dan Tata Naskah Dinas',
                '000.1'     => '000.1 - Kelembagaan dan Struktur Organisasi',
                '000.2.1'   => '000.2.1 - Telekomunikasi / Informasi Dinas',
                '000.2.2'   => '000.2.2 - Perjalanan Dinas Dalam Negeri',
                '000.3'     => '000.3 - Kerjasama Daerah / Kemitraan',
                '000.4'     => '000.4 - Dokumentasi, Publikasi & Humas',
            ],
            '070 - RISET, INOVASI & TEKNOLOGI' => [
                '071'       => '071 - Pengkajian & Penerapan Kebijakan Daerah',
                '072'       => '072 - Pengembangan Inovasi Daerah & HKI',
                '073'       => '073 - Difusi Teknologi & Fasilitasi Riset',
                '074'       => '074 - Kerjasama Penelitian & Inkubasi',
            ],
            '800 - KEPEGAWAIAN (SDM)' => [
                '800'       => '800 - Urusan Kepegawaian Umum',
                '800.1'     => '800.1 - Formasi & Pengadaan Pegawai',
                '800.2'     => '800.2 - Mutasi, Promosi & Kenaikan Pangkat',
                '800.3'     => '800.3 - Pengembangan Kompetensi & Bimtek',
                '800.4'     => '800.4 - Penilaian Kinerja (SKP) & Disiplin',
                '800.5'     => '800.5 - Kesejahteraan, Cuti & Pensiun',
            ],
            '900 - KEUANGAN & PERENCANAAN' => [
                '900'       => '900 - Pengelolaan Keuangan Daerah',
                '900.1'     => '900.1 - Anggaran (RKA/DPA)',
                '900.2'     => '900.2 - Perbendaharaan, SPJ & Pembayaran',
                '900.3'     => '900.3 - Akuntansi & Pelaporan Keuangan',
                '900.4'     => '900.4 - Pengelolaan Aset & Barang Milik Daerah (BMD)',
            ],
            '400 - KESEJAHTERAAN RAKYAT & PENDIDIKAN' => [
                '400'       => '400 - Kesejahteraan Rakyat',
                '420'       => '420 - Pendidikan',
                '421'       => '421 - Sekolah / Lembaga Pendidikan',
                '421.1'     => '421.1 - Pra Pendidikan Dasar / Magang / PKL',
                '500'       => '500 - Perekonomian & Kerjasama Bisnis',
            ]
        ];
    }

    /**
     * Format paten: [kode_berkas]/[nomor_urut]/BRIDA/[bulan_romawi]/[tahun]
     * Hasil contoh: 070/1/BRIDA/IX/2026
     */
    public function generateFormatLengkap(string $nomorBerkas, int $nomorUrut, string $tanggal, string $instansi = 'BRIDA'): string
    {
        // Ambil kode murni
        $cleanKode = trim(explode('/', $nomorBerkas)[0]);

        $date = Carbon::parse($tanggal);
        $romawi = self::getRomawi($date->month);
        $tahun = $date->year;

        return "{$cleanKode}/{$nomorUrut}/{$instansi}/{$romawi}/{$tahun}";
    }

    /**
     * Alokasi 10 baris cadangan otomatis
     */
    public function alokasikanBlokJikaBaru(string $tanggal): void
    {
        $date = Carbon::parse($tanggal);
        $tahun = $date->year;

        $sudahAda = SuratKeluar::where('tahun', $tahun)
            ->whereDate('tanggal', $date)
            ->exists();

        if (!$sudahAda) {
            $lastNomor = SuratKeluar::where('tahun', $tahun)->max('nomor_urut') ?? 0;

            $batch = [];
            for ($i = 1; $i <= 10; $i++) {
                $batch[] = [
                    'tahun'        => $tahun,
                    'tanggal'      => $date->toDateString(),
                    'nomor_urut'   => $lastNomor + $i,
                    'status'       => 'slot_kosong',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            SuratKeluar::insert($batch);
        }
    }
}