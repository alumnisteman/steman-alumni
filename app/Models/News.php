<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = ['user_id', 'title', 'slug', 'content', 'thumbnail', 'category', 'is_published'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($news) {
            if (! $news->slug) {
                $news->slug = Str::slug($news->title) . '-' . Str::random(5);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
