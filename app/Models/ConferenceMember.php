<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceMember extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'institution',
        'email',
        'profile_url',
        'is_active'
    ];

    public function committees()
    {
        return $this->belongsToMany(Committee::class, 'committee_conference_member')
                    ->withPivot('role', 'level', 'remarks', 'sort_order')
                    ->withTimestamps();
    }
}
