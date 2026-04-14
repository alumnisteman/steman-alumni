<?php

namespace App\Models;

use App\Traits\HasContentProtection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forum extends Model
{
    use HasFactory, SoftDeletes, HasContentProtection;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'views',
        'status',
    ];

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
