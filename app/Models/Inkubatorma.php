<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\InkubatormaRecord;

class Inkubatorma extends Model
{
    use HasFactory;

    protected $table = 'inkubatormas';

    public const STATUS_MENUNGGU = 'Menunggu';
    public const STATUS_AKAN_DIJADWALKAN = 'Akan Dijadwalkan';
    public const STATUS_TERJADWAL = 'Terjadwal';
    public const STATUS_SESI_KONSULTASI = 'Sesi Konsultasi';
    public const STATUS_DIJADWALKAN_ULANG = 'Dijadwalkan Ulang';
    public const STATUS_DITOLAK = 'Ditolak';
    public const STATUS_SELESAI = 'Selesai';

    protected $fillable = [
        'kode',
        'layanan_id',
        'judul_konsultasi',
        'nama_pengaju',
        'hp_pengaju',
        'opd_unit',
        'keluhan',
        'poin_asistensi',
        'tanggal_usulan',
        'jam_usulan',
        'metode_usulan',
        'target_personil_usulan',
        'layanan_lainnya',
        'lampiran',

        'status',
        'catatan_verifikator',
        'verifikasi_at',
        'verifikator_employee_id',

        'pic_employee_id',
        'tanggal_final',
        'jam_final',
        'metode_final',
        'lokasi_link_final',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_usulan'  => 'date',
        'tanggal_final'   => 'date',
        'verifikasi_at'   => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',

        'layanan_id' => 'array',
        'lampiran' => 'array',
    ];

    public static function layananOptions(): array
    {
        return [
            'penjaringan_dan_kurasi_ide'      => 'Penjaringan dan Kurasi ide Inovasi',
            'profil_inovasi'                 => 'Asistensi Penyusunan Profil Inovasi',
            'indikator_daerah'               => 'Pengisian Satuan Indikator Inovasi Daerah',
            'implementasi_dan_pilot_project' => 'Pendampingan Implementasi dan Pilot Project',
            'video_inovasi'                  => 'Pembuatan Video Inovasi',
            'kapasitas_sdm'                  => 'Penguatan Kapasitas SDM dan Inovator Riset',
            'konsultasi_tata_kelola'         => 'Konsultasi Inovasi dan Tata Kelola',
            'pembuatan_haki'                 => 'Pembuatan Hak Kekayaan Intelektual (HAKI) Inovasi',
            'hasil_inovasi_riset'            => 'Pengajuan Hasil Inovasi dalam Pelaksanaan Riset',
            'lainnya'                        => 'Lainnya',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_MENUNGGU,
            self::STATUS_AKAN_DIJADWALKAN,
            self::STATUS_TERJADWAL,
            self::STATUS_SESI_KONSULTASI,
            self::STATUS_DIJADWALKAN_ULANG,
            self::STATUS_DITOLAK,
            self::STATUS_SELESAI,
        ];
    }

    public static function statusBadgeMap(): array
    {
        return [
            self::STATUS_MENUNGGU          => 'bg-yellow-100 text-yellow-700',
            self::STATUS_AKAN_DIJADWALKAN  => 'bg-indigo-100 text-indigo-700',
            self::STATUS_TERJADWAL         => 'bg-blue-100 text-blue-700',
            self::STATUS_SESI_KONSULTASI   => 'bg-purple-100 text-purple-700',
            self::STATUS_DIJADWALKAN_ULANG => 'bg-orange-100 text-orange-700',
            self::STATUS_DITOLAK           => 'bg-red-100 text-red-700',
            self::STATUS_SELESAI           => 'bg-gray-200 text-gray-700',
        ];
    }

    public function getLayananNamaAttribute(): string
    {
        $map = self::layananOptions();
        // return $map[$this->layanan_id] ?? '—';

        if (!is_array($this->layanan_id)) {
            return $map[$this->layanan_id] ?? '—';
        }

        return collect($this->layanan_id)
            ->map(fn($id) => $map[$id] ?? $id)
            ->implode(', ');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return self::statusBadgeMap()[$this->status] ?? 'bg-gray-100 text-gray-600';
    }

    // =========================
    // RELATIONSHIPS
    // =========================

    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic_employee_id');
    }

    public function verifikatorUser()
    {
        return $this->belongsTo(User::class, 'verifikator_employee_id');
    }

    public function pic()
    {
        return $this->picUser();
    }

    public function logs()
    {
        return $this->hasMany(InkubatormaLog::class, 'inkubatorma_id')->orderBy('created_at', 'asc');
    }

    public function records()
    {
        return $this->hasMany(InkubatormaRecord::class, 'inkubatorma_id')
            ->latest();
    }
}
