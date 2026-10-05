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
    protected $fillable = [
        'slug',
        'agenda_id',
        'judul_kegiatan',
        'kategori',
        'tipe_evidence', // Added
        'file_pdf_path', // Added
        'deskripsi',     // Added
        'tanggal',
        'creator_id',
    ];

protected static function booted()
    {
        static::creating(function ($skp) {
            if (empty($skp->slug)) {
                // Potong slug judul maks 150 karakter agar total panjang slug + random string tidak melebihi varchar(191)
                $slugTitle = Str::limit(Str::slug($skp->judul_kegiatan), 150, '');
                $skp->slug = $slugTitle . '-' . Str::random(6);
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