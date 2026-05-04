<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpdKegiatan extends Model
{
    protected $table = 'ppd_kegiatans';

    protected $fillable = [
        'judul',
        'kategori',
        'hari_tanggal',
        'tempat',
        'jumlah_lembar',
        'status',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pegawai()
    {
        return $this->belongsToMany(User::class, 'ppd_kegiatan_user', 'ppd_kegiatan_id', 'user_id')
            ->withTimestamps();
    }

    public function lembar()
    {
        return $this->hasMany(PpdLembarLaporan::class, 'ppd_kegiatan_id');
    }
}