<?php

namespace App\Models;

use App\Traits\HasContentProtection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes, HasContentProtection;

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
}
