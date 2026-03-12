<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    public const ID_SELECT = [
        'aiconnect'   => '0000',
    ];

    protected $fillable = ['user_id','phone','institute_name','academic_major','part_aws_cloud_club','tracks_like','aws_familiar','comments','payment_status','coupon_code','pay_amount','production_app','application_url','logo_url','identity_no','event_attendance'];
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
