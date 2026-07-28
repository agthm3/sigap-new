<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkpFoto extends Model
{
    use HasFactory;

    protected $table = 'sigap_skp_fotos';
    protected $guarded = ['id'];

    public function skp()
    {
        return $this->belongsTo(Skp::class, 'skp_id');
    }
}