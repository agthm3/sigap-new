<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiKompetensi extends Model
{
    protected $fillable = [
        'user_id',
        'nama_sertifikat',
        'bidang_sertifikat',
        'tahun_sertifikat',
        'file_path',
        'file_name',
        'file_mime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}