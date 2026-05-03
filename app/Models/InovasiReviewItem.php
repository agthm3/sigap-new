<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InovasiReviewItem extends Model
{
    protected $fillable = [
        'inovasi_id',
        'reviewer_id',
        'field',
        'status',
        'comment',
        'point'
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