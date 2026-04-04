<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTrack extends Model
{
    use HasFactory;

    protected $fillable = ['track_id', 'name'];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function papers()
    {
        return $this->hasMany(Paper::class);
    }
}
