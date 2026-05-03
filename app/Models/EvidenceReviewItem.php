<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceReviewItem extends Model
{
    protected $fillable = ['inovasi_id','reviewer_id','no','status','comment'];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}