<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;
    protected $fillable = ['concern_name','domain_name','status','user_id'];
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public static function countProfile($domain){
        $count = User::whereHas('profile', function ($query) use ($domain) {
            $query->where('email', 'like', '%' . $domain . '%');
        })->count();
        return $count;
    }
    public static function countProfilePaid($domain){
        $count = User::whereHas('profile', function ($query) use ($domain) {
            $query->where('email', 'like', '%' . $domain . '%');
            $query->where('payment_status', '1');
        })->count();
        return $count;
    }

}
