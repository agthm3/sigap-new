<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatTemplate extends Model
{
    protected $fillable = [
        'title','category','description','lang','orientation','file_type',
        'file_path','original_name','size','privacy','access_code_hash','tags','uploaded_by'
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
