<?php

namespace App\Models;

use App\Traits\HasContentProtection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory, SoftDeletes, HasContentProtection, Searchable;

    protected $fillable = [
        'user_id',
        'content',
        'image_url',
        'type',
        'likes_count',
        'comments_count',
        'visibility',
        'is_anonymous',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Anonymous Alumni',
            'profile_picture' => null
        ]);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function tags()
    {
        return $this->morphMany(Tag::class, 'taggable');
    }

    public function taggedUsers()
    {
        return $this->belongsToMany(User::class, 'tags', 'taggable_id', 'tagged_user_id')
                    ->wherePivot('taggable_type', Post::class);
    }

    /**
     * Check if post is liked by a specific user
     */
    public function isLikedBy(?User $user)
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'content' => $this->content,
            'caption' => $this->content, // Alias for searching
            'type' => $this->type,
            'user_id' => (int) $this->user_id,
            'created_at' => $this->created_at ? $this->created_at->timestamp : null,
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::created(fn ($model) => $model->searchable());
        static::updated(fn ($model) => $model->searchable());
        static::deleted(fn ($model) => $model->unsearchable());
    }
}
