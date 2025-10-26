<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Riset extends Model
{
    protected $table = 'researches';

    protected $fillable = [
        'title','year','type','abstract','method',
        'authors','corresponding','tags','stakeholders',
        'doi','ojs_url','funding','ethics',
        'version','release_note',
        'access','access_reason','license',
        'file_path','file_name','file_size','thumbnail_path',
        'datasets',
        'stats_views','stats_downloads',
        'created_by', 'category',
        'youtube_url',
    ];

    protected $casts = [
        'authors'       => 'array',
        'corresponding' => 'array',
        'tags'          => 'array',
        'stakeholders'  => 'array',
        'datasets'      => 'array',
        'year'          => 'integer',
        'stats_views'   => 'integer',
        'stats_downloads' => 'integer',
    ];
}
