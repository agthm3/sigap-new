<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImaIndicator extends Model
{
    protected $table = 'ima_indicators';
   

    protected $fillable = [
        'no_urut', 'nama_indikator', 'deskripsi_panduan', 'video_url', 
        'parameter_options', 'file_panduan_path', 'pengali', 'pilihan_parameter'
    ];

    protected $casts = [
        'parameter_options' => 'array',
        'pilihan_parameter' => 'array',
    ];
}