<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceTemplate extends Model
{
    protected $table = 'evidence_templates';
    protected $guarded = [];

    public function params()
    {
        // ⬅️ pakai template_id (bukan default evidence_template_id)
        return $this->hasMany(EvidenceTemplateParam::class, 'template_id')
                    ->orderBy('sort_order')
                    ->orderBy('id');
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class, 'template_id');
    }
}
