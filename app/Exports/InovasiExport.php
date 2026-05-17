<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class InovasiExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    public function __construct(private Builder $query)
    {
    }

    public function query()
    {
        return $this->query;
    }

    public function title(): string
    {
        return 'Inovasi';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul',
            'OPD/Unit',
            'Inisiator Daerah',
            'Nama Inisiator',
            'Koordinat',
            'Klasifikasi',
            'Jenis Inovasi',
            'Bentuk Inovasi Daerah',
            'Asta Cipta',
            'Program Prioritas',
            'Misi Walikota',
            'Urusan Pemerintah',
            'Tahap Inovasi',
            'Asistensi Status',
            'Asistensi Note',
            'Status Aktif',
            'Status Revisi',
            'Referensi Video',
            'Review Metadata',
            'Review Evidence',
            'Lampiran Utama',
            'Dibuat Pada',
            'Diubah Pada',
        ];
    }

    public function map($inv): array
    {
        $statusAktif = (($inv->asistensi_status ?? null) === 'Disetujui') ? 'Aktif' : 'Tidak aktif';

        $statusRevisi = (
            collect($inv->reviewItems ?? [])->contains('status', 'revisi') ||
            collect($inv->evidenceReviewItems ?? [])->contains('status', 'revisi') ||
            (($inv->asistensi_status ?? null) === 'Revisi')
        ) ? 'Ada revisi' : 'Tidak';

        $videos = collect($inv->referensiVideos ?? [])
            ->map(function ($v, $i) {
                return trim(($i + 1) . '. ' . ($v->judul ?? '-') . ' | ' . ($v->video_url ?? '-'));
            })
            ->implode("\n");

        $reviewMetadata = collect($inv->reviewItems ?? [])
            ->map(function ($r) {
                $reviewer = $r->reviewer->name ?? $r->reviewer->nama ?? 'Reviewer';
                return trim(($r->field ?? '-') . ' - ' . $reviewer . ' : ' . ($r->status ?? '-'));
            })
            ->implode("\n");

        $reviewEvidence = collect($inv->evidenceReviewItems ?? [])
            ->map(function ($r) {
                $reviewer = $r->reviewer->name ?? $r->reviewer->nama ?? 'Reviewer';
                return trim('No ' . ($r->no ?? '-') . ' - ' . $reviewer . ' : ' . ($r->status ?? '-'));
            })
            ->implode("\n");

        $lampiran = collect([
            'Anggaran'      => $inv->anggaran_file ?? null,
            'Profil Bisnis' => $inv->profil_bisnis_file ?? null,
            'HAKI'          => $inv->haki_file ?? null,
            'Penghargaan'   => $inv->penghargaan_file ?? null,
        ])
            ->filter()
            ->map(fn ($path, $label) => $label . ' | ' . $path)
            ->implode("\n");

        return [
            $inv->id,
            $inv->judul,
            $inv->opd_unit ?? '-',
            $inv->inisiator_daerah ?? '-',
            $inv->inisiator_nama ?? '-',
            $inv->koordinat ?? '-',
            $inv->klasifikasi ?? '-',
            $inv->jenis_inovasi ?? '-',
            $inv->bentuk_inovasi_daerah ?? '-',
            $inv->asta_cipta ?? '-',
            $inv->program_prioritas ?? '-',
            $inv->misi_walikota ?? '-',
            $inv->urusan_pemerintah ?? '-',
            $inv->tahap_inovasi ?? '-',
            $inv->asistensi_status ?? '-',
            $inv->asistensi_note ?? '-',
            $statusAktif,
            $statusRevisi,
            $videos ?: '-',
            $reviewMetadata ?: '-',
            $reviewEvidence ?: '-',
            $lampiran ?: '-',
            optional($inv->created_at)->format('Y-m-d H:i:s'),
            optional($inv->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}