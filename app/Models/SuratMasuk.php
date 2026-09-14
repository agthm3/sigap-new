<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'surat_masuks';

    protected $fillable = [
        'tahun',
        'nomor_agenda',
        'tanggal_terima',
        'tanggal_surat',
        'asal_surat',
        'nomor_surat_masuk',
        'tingkat_surat',
        'perihal',
        'unit_pengolah',
        'diterima_oleh',
        'ttd_penerima',
        'file_surat',
    ];

    protected $casts = [
        'tahun'          => 'integer',
        'nomor_agenda'   => 'integer',
        'tanggal_terima' => 'date',
        'tanggal_surat'  => 'date',
    ];

    /**
     * Relasi ke akun user yang menerima / mencatat surat masuk
     */
    public function penerima()
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    /**
     * Accessor untuk nomor agenda berformat 3 digit (contoh: 001, 042)
     */
    public function getFormattedAgendaAttribute(): string
    {
        return sprintf('%03d', $this->nomor_agenda);
    }
}