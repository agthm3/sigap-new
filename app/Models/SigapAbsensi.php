<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SigapAbsensi extends Model
{
    protected $table = 'sigap_absensis';

    protected $fillable = [
        'user_id',
        'absen_date',
        'absen_time',
        'latitude',
        'longitude',
        'distance_meter',
        'is_outside_radius',
        'location_text',
        'photo_path',
        'keterangan',
        'late_minutes',
    ];

    protected $casts = [
        'absen_date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'distance_meter' => 'decimal:2',
        'is_outside_radius' => 'boolean',
        'late_minutes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}