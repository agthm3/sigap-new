<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatPeserta extends Model
{
    protected $fillable = [
        'kegiatan_id',
        'nomor_sertifikat',
        'nama_penerima',
        'instansi',
        'keterangan',
        'status'
    ];

    public function kegiatan()
    {
        return $this->belongsTo(SertifikatKegiatan::class,'kegiatan_id');
    }
}
