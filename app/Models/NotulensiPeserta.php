<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotulensiPeserta extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function notulensi()
    {
        return $this->belongsTo(Notulensi::class, 'notulensi_id');
    }
}