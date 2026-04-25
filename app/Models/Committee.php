<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = [
        'committee_type_id', 
        'parent_id',
        'section', 
        'name', 
        'description', 
        'sort_order'
    ];

    public function committeeType()
    {
        return $this->belongsTo(CommitteeType::class);
    }

    public function parent()
    {
        return $this->belongsTo(Committee::class, 'parent_id');
    }

    public function subCommittees()
    {
        return $this->hasMany(Committee::class, 'parent_id');
    }

    public function members()
    {
        return $this->belongsToMany(ConferenceMember::class, 'committee_conference_member')
                    ->withPivot('role', 'level', 'remarks', 'sort_order')
                    ->withTimestamps();
    }
}
