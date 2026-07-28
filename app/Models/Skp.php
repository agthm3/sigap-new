<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Skp extends Model
{
    use HasFactory;

    protected $table = 'sigap_skps';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($skp) {
            if (empty($skp->slug)) {
                // Membuat slug dari judul kegiatan + string acak unik 6 karakter
                $skp->slug = Str::slug($skp->judul_kegiatan) . '-' . Str::random(6);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function agenda()
    {
        return $this->belongsTo(SigapAgenda::class, 'agenda_id');
    }

    public function pegawais()
    {
        return $this->belongsToMany(User::class, 'sigap_skp_user', 'skp_id', 'user_id');
    }

    public function fotos()
    {
        return $this->hasMany(SkpFoto::class, 'skp_id');
    }
}