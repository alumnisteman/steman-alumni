<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPostView extends Model
{
    protected $fillable = ['user_id', 'post_id', 'view_time', 'scroll_depth'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
