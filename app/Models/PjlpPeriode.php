<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PjlpPeriode extends Model
{
    use HasFactory;

    protected $table = 'pjlp_periodes';

    protected $fillable = [
        'user_id',
        'bulan_tahun',
        'file_daftar_gaji',
        'status_laporan'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(PjlpLogbook::class, 'pjlp_periode_id')->orderBy('tanggal', 'asc');
    }
}