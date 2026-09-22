<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KgbGajiPokok extends Model
{
    use HasFactory;

    protected $table = 'kgb_gaji_pokok';

    protected $fillable = [
        'jenis_pegawai',
        'golongan',
        'masa_kerja',
        'nominal',
        'regulasi',
    ];

    public static function cariNominal($jenisPegawai, $golongan, $masaKerja)
    {
        return self::where('jenis_pegawai', $jenisPegawai)
            ->where('golongan', $golongan)
            ->where('masa_kerja', '<=', $masaKerja)
            ->orderBy('masa_kerja', 'desc')
            ->value('nominal') ?? 0;
    }
}