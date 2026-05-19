<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuseumItemLike extends Model
{
    protected $fillable = ['museum_item_id', 'user_id'];

    public function item()
    {
        return $this->belongsTo(MuseumItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
