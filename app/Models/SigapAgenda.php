<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigapAgenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'unit_title',
        'is_public',
    ];

    public function items()
    {
        return $this->hasMany(SigapAgendaItem::class);
    }
}
