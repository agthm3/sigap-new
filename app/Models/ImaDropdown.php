<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImaDropdown extends Model
{
    protected $table = 'ima_dropdowns';
protected $fillable = [
    'kategori',
    'kode',
    'icon_path',
    'warna',
    'label',
    'deskripsi',
    'targets',
    'is_active',
];
}