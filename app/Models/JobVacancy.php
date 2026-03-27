<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobVacancy extends Model
{
    protected $table = 'job_vacancies';

    protected $fillable = [
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
}
