<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagangBatch extends Model
{
    use HasFactory;

    protected $table = 'magang_batches';

    protected $fillable = [
        'nama_batch',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'kuota',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function peserta()
    {
        return $this->belongsToMany(User::class, 'magang_peserta')
                    ->withPivot('instansi_asal', 'jurusan', 'status')
                    ->withTimestamps();
    }

    public function logbooks()
    {
        return $this->hasMany(MagangLogbook::class, 'magang_batch_id');
    }
}