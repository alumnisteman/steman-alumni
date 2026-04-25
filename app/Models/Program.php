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
        'image',
        'is_event',
        'event_date',
        'event_location',
        'max_slots'
    ];

    protected $casts = [
        'is_event' => 'boolean',
        'event_date' => 'datetime',
    ];

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'published';
    }

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

    /**
     * Relationship with Program Registrations
     */
    public function registrations()
    {
        return $this->hasMany(ProgramRegistration::class);
    }
}
