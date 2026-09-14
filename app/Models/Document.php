<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'folder_id',
        'number',
        'title',
        'alias',
        'year',
        'category',
        'stakeholder',
        'description',
        'physical_rack',
        'physical_row',
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

    protected $casts = [
        'tags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_id');
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