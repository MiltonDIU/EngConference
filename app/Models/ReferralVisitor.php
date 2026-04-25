<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'referral_identification',
        'user_agent',
        'cookie_value',
        'cookie_name',
        'minutes',
        'user_id',
        'updated_at',
        'deleted_at',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function referral(){
        return $this->belongsTo(Referral::class, 'referral_identification', 'identification');
    }
}
