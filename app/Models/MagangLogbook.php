<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagangLogbook extends Model
{
    use HasFactory;

    protected $table = 'magang_logbooks';

    protected $fillable = [
        'magang_batch_id',
        'user_id',
        'tanggal',
        'kategori',
        'kegiatan',
        'file_lampiran',
        'status_verifikasi',
        'catatan_revisi',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'verified_at' => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(MagangBatch::class, 'magang_batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}