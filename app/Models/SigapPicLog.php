<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SigapPicLog extends Model
{
    protected $fillable = [
        'user_id',
        'system_id',
        'aksi',
        'detail',
        'ip_address',
        'user_agent',
    ];

    public function system()
    {
        return $this->belongsTo(SigapPicSystem::class, 'system_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}