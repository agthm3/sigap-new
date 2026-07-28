<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SkpKumpulan extends Model
{
    use HasFactory;

    protected $table = 'sigap_skp_kumpulans';

    protected $fillable = [
        'slug',
        'user_id',
        'kategori',
        'bulan_tahun',
        'judul_kumpulan',
        'skp_ids',
        'ppd_ids'
    ];

    protected $casts = [
        'skp_ids' => 'array',
        'ppd_ids' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul_kumpulan) . '-' . Str::random(6);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}