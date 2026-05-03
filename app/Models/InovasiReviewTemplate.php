<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InovasiReviewTemplate extends Model
{
    protected $fillable = [
        'field',
        'label',
        'point'
    ];
}