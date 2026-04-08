<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuccessStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'major_year',
        'quote',
        'content',
        'image_path',
        'user_id',
        'is_published',
        'order'
    ];

    /**
     * Get the user associated with the success story.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
