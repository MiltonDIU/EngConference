<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'designation',
        'department',
        'institution',
        'country_id',
        'registration_id',
        'whatsapp_number',
        'is_author',
        'participation_mode',
        'pay_amount',
        'payment_status',
        'currency',
        'author_list_confirmed',
    ];

    protected $appends = [
        'phone',
    ];

    protected $casts = [
        'is_author' => 'boolean',
        'author_list_confirmed' => 'boolean',
    ];

    public function getPhoneAttribute()
    {
        return $this->whatsapp_number;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
