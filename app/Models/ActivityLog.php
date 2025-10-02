<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id','user_name','user_role',
        'module','action','object_type','object_id',
        'object_title','object_alias','sensitivity',
        'success','reason','ip_address','user_agent',
    ];

    // Relasi opsional
    public function user() { return $this->belongsTo(User::class); }
}
