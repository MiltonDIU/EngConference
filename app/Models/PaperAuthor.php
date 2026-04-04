<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaperAuthor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'paper_id',
        'name',
        'designation',
        'department',
        'institution',
        'country_id',
        'email',
        'is_presenting_author',
        'author_order',
    ];

    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
