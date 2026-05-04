<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpdLembarFoto extends Model
{
    protected $table = 'ppd_lembar_fotos';

    protected $fillable = [
        'ppd_lembar_laporan_id',
        'foto_path',
        'urutan',
    ];

    public function lembar()
    {
        return $this->belongsTo(PpdLembarLaporan::class, 'ppd_lembar_laporan_id');
    }
}