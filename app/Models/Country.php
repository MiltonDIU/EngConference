<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'sort_order',
        'is_active',
    ];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}
