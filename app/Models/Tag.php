<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['tagged_user_id', 'taggable_id', 'taggable_type'];

    public function user()
    {
        return $this->belongsTo(User::class, 'tagged_user_id');
    }

    public function taggable()
    {
        return $this->morphTo();
    }
}
