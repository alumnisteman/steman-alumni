<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_desktop',
        'image_mobile',
        'desktop_offset_x',
        'desktop_offset_y',
        'desktop_zoom',
        'mobile_offset_x',
        'mobile_offset_y',
        'mobile_zoom',
        'link',
        'position',
        'start_date',
        'end_date',
        'is_active',
        'click'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saved(function ($ad) {
            \Illuminate\Support\Facades\Cache::forget('active_ads');
        });

        static::deleting(function ($ad) {
            if ($path = $ad->getRawOriginal('image_desktop')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
            if ($mobilePath = $ad->getRawOriginal('image_mobile')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($mobilePath);
            }
            \Illuminate\Support\Facades\Cache::forget('active_ads');
        });
    }


    /**
     * Scope a query to only include active and scheduled ads.
     */
    public function scopeActive($query)
    {
        $today = now()->startOfDay();
        return $query->where('is_active', true)
            ->where(function($q) use ($today) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $today);
            })
            ->where(function($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today);
            });
    }

    /**
     * Scope a query to ads in a specific position.
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Get the correct URL for the ad image.
     */
    public function getImageDesktopAttribute($value)
    {
        return $this->resolveImageUrl($value);
    }

    /**
     * Get the correct URL for the mobile ad image.
     */
    public function getImageMobileAttribute($value)
    {
        // Fallback to desktop image if no mobile image is set
        if (!$value) {
            return $this->image_desktop;
        }
        return $this->resolveImageUrl($value);
    }

    /**
     * Internal helper to resolve storage URLs.
     */
    private function resolveImageUrl($value)
    {
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        $path = ltrim($value, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        
        return asset('storage/' . ltrim($path, '/'));
    }
}
