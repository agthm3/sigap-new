<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpjBidang extends Model
{
    protected $guarded = ['id'];

    public function subKegiatans()
    {
        return $this->hasMany(SpjSubKegiatan::class, 'spj_bidang_id');
    }
}