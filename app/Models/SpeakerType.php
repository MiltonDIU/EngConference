<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakerType extends Model
{
    use HasFactory;
    protected $table = 'speaker_types';
    protected $fillable = ['title','slug','publication_status'];
    public function speaker(){
        return $this->hasMany(Speaker::class,'speaker_type_id','id');
    }
}
