<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inovasi extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'judul','opd_unit','inisiator_daerah','inisiator_nama','koordinat',
        'klasifikasi','jenis_inovasi','bentuk_inovasi_daerah','asta_cipta','program_prioritas',
        'urusan_pemerintah','waktu_uji_coba','waktu_penerapan','tahap_inovasi',
        'rancang_bangun','tujuan','manfaat','hasil_inovasi','perkembangan_inovasi',
        'anggaran_file','profil_bisnis_file','haki_file','penghargaan_file',
    ];

    protected $casts = [
        'waktu_uji_coba'  => 'date',
        'waktu_penerapan' => 'date',
    ];
}
