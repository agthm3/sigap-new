<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImaSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'fase_nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];
}