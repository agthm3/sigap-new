<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SigapPicSystem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_sistem',
        'slug',
        'kategori',
        'deskripsi',
        'url',
        'youtube_url',
        'thumbnail_path',
        'status',
        'level_kritis',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['primary_pic_name', 'youtube_embed_url'];

    public function assignments()
    {
        return $this->hasMany(SigapPicAssignment::class, 'system_id')->orderBy('urutan');
    }

    public function credentials()
    {
        return $this->hasMany(SigapPicCredential::class, 'system_id');
    }

    public function logs()
    {
        return $this->hasMany(SigapPicLog::class, 'system_id')->latest();
    }

    public function getPrimaryPicNameAttribute(): ?string
    {
        $primary = $this->assignments->firstWhere('is_primary', true) ?: $this->assignments->first();

        return $primary?->user?->name ?? $primary?->nama_pic;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->youtube_url) {
            return null;
        }

        $url = $this->youtube_url;

        if (preg_match('/(?:youtu\.be\/|v=|embed\/|shorts\/)([A-Za-z0-9_-]{11})/i', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        return null;
    }
}