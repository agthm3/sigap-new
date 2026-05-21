<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SigapDaftarHadirPeserta extends Model
{
    protected $table = 'sigap_daftar_hadir_peserta';

    protected $fillable = [
        'kegiatan_id',
        'nama',
        'instansi',
        'gender',
        'no_hp',
        'email',
        'ttd_path',
        'urutan_absen',
        'created_by',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(SigapDaftarHadirKegiatan::class, 'kegiatan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}