<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigapAgendaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sigap_agenda_id',
        'order_no',
        'mode',
        'assignees',
        'description',
        'time_text',
        'place',
        'file_path',
    ];

    public function agenda()
    {
        return $this->belongsTo(SigapAgenda::class);
    }
}
