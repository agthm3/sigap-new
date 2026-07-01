<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SigapDaftarHadirKegiatan extends Model
{
    protected $table = 'sigap_daftar_hadir_kegiatan';

    protected $fillable = [
        'uuid',
        'nama_kegiatan',
        'hari_tanggal',
        'tempat',
        'waktu',
        'status',
        'created_by',
        'undangan_path',
        'buat_sertifikat',
        'nomor_surat'
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(SigapDaftarHadirPeserta::class, 'kegiatan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Satu kegiatan memiliki satu penandatangan (opsional).
     */
    public function penandatangan(): HasOne
    {
        return $this->hasOne(SigapDaftarHadirPenandatangan::class, 'kegiatan_id');
    }
}