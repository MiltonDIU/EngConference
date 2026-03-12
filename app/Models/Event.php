<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Event extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    public $table = 'events';

    protected $appends = [
        'photo',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'slug',
        'sub_title',
        'youtube_link',
        'contact_phone',
        'contact_email',
        'contact_address',
        'about_where',
        'about_when',
        'about_summary',
        'description',
        'date_time',
        'reg_close_time',
        'speaker_title',
        'speaker_sub_title',
        'schedule_title',
        'schedule_sub_title',
        'venue_title',
        'venue_sub_title',
        'hotel_sub_title',
        'gallery_title',
        'gallery_sub_title',
        'sponsors_title',
        'sponsors_sub_title',
        'faq_title',
        'faq_sub_title',
        'newsletter_title',
        'newsletter_sub_title',
        'ticket_title',
        'ticket_sub_title',
        'contact_title',
        'is_active',
        'is_free',
        'is_events',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(50)->height(50);
    }
    public function getPhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
        }
        return $file;
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class);
    }
    public function hotels()
    {
        return $this->belongsToMany(Hotel::class);
    }

    public function venues()
    {
        return $this->belongsToMany(Venue::class);
    }

    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class);
    }

    public function faqs()
    {
        return $this->belongsToMany(Faq::class);
    }

    public function prices()
    {
        return $this->belongsToMany(Price::class);
    }
    public function schedule(){
        return $this->hasMany(Schedule::class, 'event_id','id');
    }
    public function gallery(){
        return $this->hasMany(Gallery::class, 'event_id','id');
    }

    public function setDateTimeAttribute($value)
    {
        $this->attributes['date_time'] = Carbon::parse($value);

    }
}
