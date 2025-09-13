<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceTemplateParam extends Model
{
    protected $table = 'evidence_template_params';

    protected $fillable = [
        'template_id','label','weight','sort_order',
    ];

    public function template()
    {
        return $this->belongsTo(EvidenceTemplate::class, 'template_id');
    }
}
