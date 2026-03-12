<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = ['title','value','expire_date','email','user_id','publication_status','use_status','is_domain'];
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function referral(){
        return $this->hasOne(Referral::class, 'coupon_id', 'id');
    }
    public static function countProfile($id){
        $count = Profile::where('coupon_code',$id)->count();
        return $count;
    }
    public static function countProfilePaid($id){
        $count = Profile::where('coupon_code',$id)->where('payment_status','1')->count();
        return $count;
    }
}
