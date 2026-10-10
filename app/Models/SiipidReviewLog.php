<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiipidReviewLog extends Model
{
    use HasFactory;

    protected $table = 'siipid_review_logs';

    protected $fillable = [
        'prestasi_id',
        'reviewer_id',
        'status_sebelumnya',
        'status_baru',
        'catatan',
    ];

    public function prestasi()
    {
        return $this->belongsTo(SiipidPrestasi::class, 'prestasi_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
