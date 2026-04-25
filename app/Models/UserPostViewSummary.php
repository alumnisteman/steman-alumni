<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPostViewSummary extends Model
{
    protected $fillable = ['user_id', 'post_id', 'total_view_time', 'views_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
