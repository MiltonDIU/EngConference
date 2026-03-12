<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'schedule_id',
        'created_at',
        'updated_at'
    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function schedule(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
