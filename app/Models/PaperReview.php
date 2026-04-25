<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperReview extends Model
{
    protected $fillable = [
        'paper_id',
        'reviewed_by',
        'status',
        'review_note',
    ];

    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
