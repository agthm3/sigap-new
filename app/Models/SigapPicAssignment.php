<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SigapPicAssignment extends Model
{
    protected $fillable = [
        'system_id',
        'user_id',
        'pegawai_nik',
        'nama_pic',
        'jabatan_pic',
        'bidang',
        'tanggung_jawab',
        'is_primary',
        'urutan',
        'catatan',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function system()
    {
        return $this->belongsTo(SigapPicSystem::class, 'system_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}