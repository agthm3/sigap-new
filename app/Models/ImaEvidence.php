<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImaEvidence extends Model
{
    protected $table = 'ima_evidences';
    
    protected $fillable = [
        'ima_inovasi_id', 'ima_indicator_id', 'no_urut', 'parameter_label', 
        'parameter_weight', 'deskripsi', 'link_url', 'review_status', 'review_note'
    ];

    public function inovasi()
    {
        return $this->belongsTo(ImaInovasi::class, 'ima_inovasi_id');
    }

    public function indicator()
    {
        return $this->belongsTo(ImaIndicator::class, 'ima_indicator_id');
    }

    public function files()
    {
        return $this->hasMany(ImaEvidenceFile::class, 'ima_evidence_id');
    }
}