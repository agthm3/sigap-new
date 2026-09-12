<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImaEvidenceFile extends Model
{
    protected $table = 'ima_evidence_files';
    
    protected $fillable = [
        'ima_evidence_id', 'file_path', 'file_name', 'file_mime', 
        'file_size', 'nomor_surat', 'tanggal_surat', 'tentang'
    ];

    public function evidence()
    {
        return $this->belongsTo(ImaEvidence::class, 'ima_evidence_id');
    }
}