<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paper extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'submission_id',
        'track_id',
        'sub_track_id',
        'title',
        'abstract',
        'keywords',
        'mode_of_participation',
        'is_corresponding_author',
        'has_multiple_authors',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function subTrack()
    {
        return $this->belongsTo(SubTrack::class, 'sub_track_id');
    }

    public function authors()
    {
        return $this->hasMany(PaperAuthor::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reviewHistory()
    {
        return $this->hasMany(PaperReview::class)->latest();
    }
}
