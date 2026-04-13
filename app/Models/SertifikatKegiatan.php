<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatKegiatan extends Model
{
    protected $fillable = [
        'nama_kegiatan',
        'jenis',
        'tanggal',
        'keterangan',
        'status'
    ];

    public function sertifikat()
    {
        return $this->hasMany(SertifikatPeserta::class,'kegiatan_id');
    }
}
