<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name','username','nip','unit','role','status','phone','email','avatar_path',
    ];
}
