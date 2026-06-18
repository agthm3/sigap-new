<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InovasiExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $query;

    public function __construct($query)
    {
        // Eager load relasi evidences dan evidenceFiles untuk optimalisasi performa (mencegah N+1)
        $this->query = $query->with(['evidences.evidenceFiles']);
    }

    /**
     * Mengembalikan query builder untuk data yang akan diexport.
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * Menyusun susunan Header Kolom Excel.
     */
    public function headings(): array
    {
        // 1. Kolom Metadata Utama
        $metadataHeaders = [
            'ID Inovasi',
            'Judul Inovasi',
            'OPD / Unit Kerja',
            'Inisiator Daerah',
            'Nama Inisiator',
            'Koordinat',
            'Klasifikasi',
            'Jenis Inovasi',
            'Bentuk Inovasi Daerah',
            'Asta Cipta',
            'Program Prioritas Walikota',
            'Misi Walikota',
            'Urusan Pemerintah',
            'Waktu Uji Coba',
            'Waktu Penerapan',
            'Perkembangan Inovasi',
            'Rancang Bangun',
            'Tujuan Inovasi',
            'Manfaat Inovasi',
            'Hasil Inovasi',
            'Status Asistensi',
            'Catatan Asistensi',
            'Tanggal Dibuat'
        ];

        // 2. Ambil Judul Indikator Evidence secara dinamis dari tabel template seeder (no 1-20)
        $evidenceHeaders = DB::table('evidence_templates')
            ->orderBy('no')
            ->pluck('indikator')
            ->map(function ($indikator, $index) {
                // Bersihkan text dari karakter bintang berlebih jika diperlukan agar rapi di excel
                return ($index + 1) . '. ' . trim(str_replace('*', '', $indikator));
            })
            ->toArray();

        // Fallback jika seeder belum dijalankan atau tabel kosong
        if (empty($evidenceHeaders)) {
            for ($i = 1; $i <= 20; $i++) {
                $evidenceHeaders[] = "Evidence " . $i;
            }
        }

        return array_merge($metadataHeaders, $evidenceHeaders);
    }

    /**
     * Melakukan mapping data dari setiap baris model Inovasi ke baris Excel.
     */
    public function map($inovasi): array
    {
        // Mapping kolom metadata dasar
        $row = [
            $inovasi->id,
            $inovasi->judul,
            $inovasi->opd_unit,
            $inovasi->inisiator_daerah,
            $inovasi->inisiator_nama,
            $inovasi->koordinat,
            $inovasi->klasifikasi,
            $inovasi->jenis_inovasi,
            $inovasi->bentuk_inovasi_daerah,
            $inovasi->asta_cipta,
            $inovasi->program_prioritas,
            $inovasi->misi_walikota,
            $inovasi->urusan_pemerintah,
            $inovasi->waktu_uji_coba,
            $inovasi->waktu_penerapan,
            $inovasi->perkembangan_inovasi,
            strip_tags($inovasi->rancang_bangun), // hilangkan tag html jika input berupa rich text
            strip_tags($inovasi->tujuan),
            strip_tags($inovasi->manfaat),
            strip_tags($inovasi->hasil_inovasi),
            $inovasi->asistensi_status ?? 'Menunggu Verifikasi',
            $inovasi->asistensi_note ?? '-',
            $inovasi->created_at ? $inovasi->created_at->format('Y-m-d H:i:s') : '',
        ];

        // Kelompokkan relasi evidences berdasarkan kolom 'no' agar pencarian bernilai O(1)
        $evidencesByNo = collect();
        if ($inovasi->relationLoaded('evidences') || isset($inovasi->evidences)) {
            $evidencesByNo = $inovasi->evidences->keyBy('no');
        } else {
            // Fallback aman jika relasi belum didefinisikan di model Inovasi
            $evidencesByNo = \App\Models\Evidence::where('inovasi_id', $inovasi->id)
                ->with('evidenceFiles')
                ->get()
                ->keyBy('no');
        }

        // 3. Loop dari nomor 1 sampai 20 untuk mengecek keberadaan file/evidence
        for ($no = 1; $no <= 20; $no++) {
            $statusIsi = 0; // default 0 jika kosong

            if (isset($evidencesByNo[$no])) {
                $evidence = $evidencesByNo[$no];

                // Cek apakah ada file terupload di tabel child (evidenceFiles) 
                // atau kolom link_url/file_path bawaan terisi
                $hasFiles = false;
                if (isset($evidence->evidenceFiles) && $evidence->evidenceFiles->count() > 0) {
                    $hasFiles = true;
                } else {
                    // Fallback query langsung jika relasi nested tidak terpanggil
                    $hasFiles = DB::table('evidence_files')
                        ->where('evidence_id', $evidence->id)
                        ->exists();
                }

                if ($hasFiles || !empty($evidence->link_url) || !empty($evidence->file_path)) {
                    $statusIsi = 1; // isi 1 jika ada file / terisi
                }
            }

            $row[] = $statusIsi;
        }

        return $row;
    }

    /**
     * Memberikan style styling profesional pada spreadsheet.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Row pertama (Header) dibuat Bold dengan background Maroon (identitas SIGAP) dan teks putih
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '800000'] // Warna Maroon Dinas
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ],
        ];
    }
}