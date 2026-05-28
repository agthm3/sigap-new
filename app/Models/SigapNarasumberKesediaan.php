<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigapNarasumberKesediaan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke tabel Kegiatan Daftar Hadir
    public function kegiatan()
    {
        return $this->belongsTo(SigapDaftarHadirKegiatan::class, 'kegiatan_id');
    }

    protected $casts = [
        'signed_at' => 'datetime',
    ];
}