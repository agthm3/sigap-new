<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'number',
        'title',
        'alias',
        'year',
        'category',
        'stakeholder',
        'description',
        'tags',
        'sensitivity',
        'related_user_id',
        'version',
        'doc_date',
        'file_path',
        'thumb_path',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
