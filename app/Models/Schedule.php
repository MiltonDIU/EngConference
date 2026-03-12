<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    public $table = 'schedules';

    public const IS_WORKSHOP_SELECT = [
        '0' => 'No',
        '1' => 'Yes',
    ];

    public const IS_Active = [
        '0' => 'No',
        '1' => 'Yes',
    ];

    public const IS_event_session = [
        '0' => 'No',
        '1' => 'Yes',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'subtitle',
        'day_number',
        'start_time',
        'speaker_id',
        'total_seat',
        'is_workshop',
        'event_session',
        'benefits',
        'tools',
        'about',
        'event_id',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public function feedback()
    {
        return $this->hasMany(Schedule::class);
    }
    public function speaker()
    {
        return $this->belongsTo(Speaker::class, 'speaker_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function attendance(){
        return $this->hasMany(Attendance::class,'schedule_id','id');
    }
    public function speakers()
    {
        return $this->belongsToMany(Speaker::class);
    }

}
