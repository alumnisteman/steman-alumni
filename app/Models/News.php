<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    use SoftDeletes;
    use \App\Traits\HasPublishStatus;

    protected $fillable = ['user_id', 'title', 'slug', 'content', 'thumbnail', 'category', 'status', 'is_published'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($news) {
            if (! $news->slug) {
                $news->slug = Str::slug($news->title) . '-' . Str::random(5);
            }
        });
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->isPublished();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Admin/Deleted User'
        ]);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
