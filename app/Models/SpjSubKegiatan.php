<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class SpjSubKegiatan extends Model
{
    protected $guarded = ['id'];
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function bidang()
    {
        return $this->belongsTo(SpjBidang::class, 'spj_bidang_id');
    }

    public function kegiatans()
    {
        return $this->hasMany(SpjKegiatan::class, 'spj_sub_kegiatan_id');
    }
}