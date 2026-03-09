<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceFile extends Model
{
    protected $fillable = [
        'evidence_id',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
        'nomor_surat',
        'tanggal_surat',
        'tentang',
    ];

    public function evidence()
    {
        return $this->belongsTo(Evidence::class);
    }
}
