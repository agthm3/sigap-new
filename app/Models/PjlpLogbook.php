<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PjlpLogbook extends Model
{
    use HasFactory;

    protected $table = 'pjlp_logbooks';

    protected $fillable = [
        'pjlp_periode_id',
        'tanggal',
        'hari',
        'deskripsi_pekerjaan',
        'foto_evidence',    // Kolom lama dipertahankan untuk backward compatibility
        'foto_evidences',   // Kolom baru untuk multi-foto (array)
        'status',
        'catatan_verifikator',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'foto_evidences' => 'array', // Otomatis mengkonversi JSON database ke Array PHP
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PjlpPeriode::class, 'pjlp_periode_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}