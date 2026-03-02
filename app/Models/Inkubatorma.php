<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inkubatorma extends Model
{
    use HasFactory;

    protected $table = 'inkubatormas';

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
    ];

    public function getLayananNamaAttribute(): string
    {
        $map = self::layananOptions();
        return $map[$this->layanan_id] ?? '—';
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

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'Menunggu' => 'bg-yellow-100 text-yellow-700',
            'Akan Dijadwalkan' => 'bg-indigo-100 text-indigo-700',
            'Terjadwal' => 'bg-blue-100 text-blue-700',
            'Dijadwalkan Ulang' => 'bg-orange-100 text-orange-700',
            'Ditolak' => 'bg-red-100 text-red-700',
            'Selesai' => 'bg-gray-200 text-gray-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
