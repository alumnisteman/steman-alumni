<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Podcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'guest_name',
        'description',
        'audio_url',
        'thumbnail_url',
        'duration',
        'category',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($podcast) {
            if (empty($podcast->slug)) {
                $podcast->slug = Str::slug($podcast->title) . '-' . Str::random(5);
            }
        });
    }

    public function getAudioLinkAttribute()
    {
        if (Str::startsWith($this->audio_url, ['http://', 'https://'])) {
            return $this->audio_url;
        }
        return asset('storage/' . $this->audio_url);
    }

    public function getThumbnailLinkAttribute()
    {
        if (empty($this->thumbnail_url)) {
            return 'https://images.unsplash.com/photo-1478737270239-2fccd2c7862a?q=80&w=2070&auto=format&fit=crop';
        }
        if (Str::startsWith($this->thumbnail_url, ['http://', 'https://'])) {
            return $this->thumbnail_url;
        }
        return asset('storage/' . $this->thumbnail_url);
    }
}
