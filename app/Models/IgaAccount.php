<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IgaAccount extends Model
{
    use HasFactory;

    
    protected $table = 'iga_accounts';

    
    protected $fillable = [
        'role',
        'daerah',
        'opd',
        'username',
        'password_raw',
    ];
}