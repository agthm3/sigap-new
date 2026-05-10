<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SigapPicCredential extends Model
{
    protected $fillable = [
        'system_id',
        'nama_akun',
        'username',
        'password_encrypted',
        'email',
        'url_login',
        'access_level',
        'is_sensitive',
        'catatan',
    ];

    protected $casts = [
        'password_encrypted' => 'encrypted',
        'is_sensitive' => 'boolean',
    ];

    protected $hidden = [
        'password_encrypted',
    ];

    public function system()
    {
        return $this->belongsTo(SigapPicSystem::class, 'system_id');
    }
}