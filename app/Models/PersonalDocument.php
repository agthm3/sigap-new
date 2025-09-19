<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalDocument extends Model
{
    protected $fillable = [
        'user_id','type','title','path','mime','size','status','verified_at','notes','uploaded_by',
        'access_code_hash','access_code_hint','access_code_set_at','access_code_enc'
    ];

    protected $casts = ['verified_at' => 'datetime'];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
