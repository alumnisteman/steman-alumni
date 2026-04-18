<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'url',
    ];

    /**
     * Relationship back to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get fontawesome icon for the platform
     */
    public function getIconAttribute()
    {
        $icons = [
            'instagram' => 'bi bi-instagram',
            'facebook' => 'bi bi-facebook',
            'tiktok' => 'bi bi-tiktok',
            'linkedin' => 'bi bi-linkedin',
            'github' => 'bi bi-github',
            'website' => 'bi bi-globe',
        ];

        return $icons[$this->platform] ?? 'bi bi-link-45deg';
    }
}
