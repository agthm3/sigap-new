<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kinerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'category','rhk','title','description','activity_date',
        'file_path','file_mime','file_size','thumb_path','created_by'
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];
}
