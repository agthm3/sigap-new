<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;
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
