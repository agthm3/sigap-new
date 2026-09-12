<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImaDropdown extends Model
{
    protected $table = 'ima_dropdowns';
    
    protected $fillable = [
        'kategori', 'kode', 'label', 'is_active'
    ];
}