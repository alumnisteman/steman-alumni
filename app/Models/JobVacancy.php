<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobVacancy extends Model
{
    use SoftDeletes;

    protected $table = 'job_vacancies';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'company',
        'location',
        'description',
        'content',
        'external_link',
        'type',
        'status',
        'image'
    ];

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title) . '-' . Str::random(5);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
