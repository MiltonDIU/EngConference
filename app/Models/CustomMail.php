<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomMail extends Model
{
    use HasFactory;
    protected $fillable = ['subject','mail_body','user_id','publication_status'];
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
