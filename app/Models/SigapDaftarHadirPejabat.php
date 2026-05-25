<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SigapDaftarHadirPejabat extends Model
{
    protected $table = 'sigap_daftar_hadir_pejabat';

    protected $fillable = [
        'nama_lengkap',
        'jabatan',
        'pangkat',
        'golongan',
        'nip',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function penandatangan(): HasMany
    {
        return $this->hasMany(SigapDaftarHadirPenandatangan::class, 'pejabat_id');
    }
}