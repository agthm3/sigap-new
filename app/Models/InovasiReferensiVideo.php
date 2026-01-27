<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InovasiReferensiVideo extends Model
{
    protected $table = 'inovasi_referensi_video';

    protected $fillable = [
        'inovasi_id',
        'judul',
        'deskripsi',
        'video_url',
    ];
}
