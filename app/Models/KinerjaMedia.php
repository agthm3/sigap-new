<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KinerjaMedia extends Model
{
    protected $table = 'kinerja_media';

    protected $fillable = [
        'kinerja_id', 'path', 'mime', 'size', 'is_image', 'is_primary',
    ];

    public function kinerja()
    {
        return $this->belongsTo(Kinerja::class);
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path ? Storage::disk('public')->url($this->path) : null;
    }
}
