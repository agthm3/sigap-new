<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InovasiReview extends Model
{
    protected $fillable = [
        'inovasi_id',
        'reviewer_id',
        'nilai_rancang_bangun',
        'nilai_manfaat',
        'nilai_kebaruan',
        'nilai_dampak',
        'nilai_evidence',
        'catatan_rancang_bangun',
        'catatan_manfaat',
        'catatan_evidence',
        'catatan_umum',
        'rekomendasi',
        'status',
        'reviewed_at'
    ];

    public function inovasi()
    {
        return $this->belongsTo(Inovasi::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}