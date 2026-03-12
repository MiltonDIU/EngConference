<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsorType extends Model
{
    use HasFactory;
    protected $fillable = ['title','serial','is_active'];
    public function sponsor(){
        return $this->hasMany(Sponsor::class);
    }
}
