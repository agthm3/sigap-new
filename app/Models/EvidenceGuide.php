<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceGuide extends Model
{
    protected $fillable = [
        'no',
        'indikator',
        'deskripsi',
        'file_path',
        'file_name',
    ];
}