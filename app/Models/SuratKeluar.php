<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluars';

    protected $fillable = [
        'tahun',
        'tanggal',
        'nomor_urut',
        'nomor_berkas',
        'nomor_surat_lengkap',
        'alamat_penerima',
        'perihal',
        'created_by',
        'status',
        'file_surat',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nomor_urut' => 'integer',
        'tahun' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}