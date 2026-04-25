<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function committees()
    {
        return $this->hasMany(Committee::class);
    }
}
