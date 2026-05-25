<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SigapDaftarHadirPenandatangan extends Model
{
    protected $table = 'sigap_daftar_hadir_penandatangan';

    protected $fillable = [
        'uuid',
        'kegiatan_id',
        'pejabat_id',
        'nama_lengkap',
        'jabatan',
        'pangkat',
        'golongan',
        'nip',
        'tempat_ttd',
        'tanggal_ttd',
        'ttd_path',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(SigapDaftarHadirKegiatan::class, 'kegiatan_id');
    }

    public function pejabat(): BelongsTo
    {
        return $this->belongsTo(SigapDaftarHadirPejabat::class, 'pejabat_id');
    }

    /**
     * Apakah pejabat sudah menandatangani?
     */
    public function getSudahTtdAttribute(): bool
    {
        return $this->ttd_path !== null && $this->signed_at !== null;
    }
}