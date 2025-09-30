<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatAccessLog extends Model
{
    protected $fillable = [
        'format_template_id','user_id','action','success','ip','user_agent'
    ];
}
