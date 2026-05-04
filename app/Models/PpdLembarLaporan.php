<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpdLembarLaporan extends Model
{
    protected $table = 'ppd_lembar_laporans';

    protected $fillable = [
        'ppd_kegiatan_id',
        'user_id',
        'lembar_ke',
        'deskripsi',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(PpdKegiatan::class, 'ppd_kegiatan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fotos()
    {
        return $this->hasMany(PpdLembarFoto::class, 'ppd_lembar_laporan_id');
    }
}