<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function subTracks()
    {
        return $this->hasMany(SubTrack::class);
    }

    public function papers()
    {
        return $this->hasMany(Paper::class);
    }
}
