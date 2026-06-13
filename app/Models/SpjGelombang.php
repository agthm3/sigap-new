<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpjGelombang extends Model
{
    protected $guarded = ['id'];

    public function kegiatan()
    {
        return $this->belongsTo(SpjKegiatan::class, 'spj_kegiatan_id');
    }
}