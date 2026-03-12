<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'posts';

    protected $appends = [
        'feature_image',
    ];

    public const IS_ACTIVE_SELECT = [
        '1' => 'Yes',
        '0' => 'No',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'details',
        'user_id',
        'blog_category_id',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 600, 400);
        $this->addMediaConversion('blog_list')->fit('crop', 502, 284);
        $this->addMediaConversion('feature')->fit('crop', 1074, 609);
        $this->addMediaConversion('top_article_image')->fit('crop', 231, 130);
    }

    public function postComments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function blog_category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getFeatureImageAttribute()
    {
        $file = $this->getMedia('feature_image')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
            $file->blog_list   = $file->getUrl('blog_list');
            $file->feature   = $file->getUrl('feature');
            $file->top_article_image   = $file->getUrl('top_article_image');
        }

        return $file;
    }
    public function like()
    {
        return $this->hasMany(Like::class);
    }
}
