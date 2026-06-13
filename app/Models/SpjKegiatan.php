<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpjKegiatan extends Model
{
    protected $guarded = ['id'];

    public function subKegiatan()
    {
        return $this->belongsTo(SpjSubKegiatan::class, 'spj_sub_kegiatan_id');
    }

    public function gelombangs()
    {
        return $this->hasMany(SpjGelombang::class, 'spj_kegiatan_id');
    }
}