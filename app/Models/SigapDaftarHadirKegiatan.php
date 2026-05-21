<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(SigapDaftarHadirPeserta::class, 'kegiatan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}