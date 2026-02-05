<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InkubatormaLog extends Model
{
    protected $table = 'inkubatorma_logs';

    public $timestamps = false; // karena tabel log biasanya hanya created_at
    protected $fillable = [
        'inkubatorma_id','aksi','status_dari','status_ke','catatan','created_by','created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function inkubatorma()
    {
        return $this->belongsTo(Inkubatorma::class, 'inkubatorma_id');
    }
}
