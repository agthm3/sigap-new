<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    protected $table = 'evidences';

    protected $fillable = [
        'inovasi_id','template_id','no','indikator',
        'parameter_label','parameter_weight',
        'jenis_file','deskripsi',
        'file_path','file_name','file_mime','file_size',
        'link_url','review_status','review_note',
    ];

    public function inovasi()
    {
        return $this->belongsTo(Inovasi::class);
    }

    public function template()
    {
        return $this->belongsTo(EvidenceTemplate::class, 'template_id');
    }
    public function files()
    {
        return $this->hasMany(EvidenceFile::class);
    }

}
