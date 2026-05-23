<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
    protected static function booted()
    {
        static::creating(function ($doc) {
            if (empty($doc->public_key)) {
                $doc->public_key = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'public_key';
    }
}
