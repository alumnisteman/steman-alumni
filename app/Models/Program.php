<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Program extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'icon',
        'content',
        'status',
        'registration_link',
        'image'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($program) {
            if (empty($program->slug)) {
                $program->slug = Str::slug($program->title);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
