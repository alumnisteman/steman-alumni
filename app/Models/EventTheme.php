<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EventTheme extends Model
{
    protected $fillable = [
        'name', 'description',
        'start_month', 'start_day', 'end_month', 'end_day',
        'primary_color', 'secondary_color', 'accent_color',
        'css_class', 'banner_text', 'banner_subtext', 'banner_icon', 'emoji',
        'show_countdown', 'countdown_label', 'countdown_month', 'countdown_day',
        'priority', 'is_active',
        'is_islamic', 'hijri_month', 'hijri_day', 'hijri_duration',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'show_countdown' => 'boolean',
        'is_islamic'     => 'boolean',
        'priority'       => 'integer',
        'start_month'    => 'integer',
        'start_day'      => 'integer',
        'end_month'      => 'integer',
        'end_day'        => 'integer',
        'countdown_month'=> 'integer',
        'countdown_day'  => 'integer',
        'hijri_month'    => 'integer',
        'hijri_day'      => 'integer',
        'hijri_duration' => 'integer',
    ];

    /**
     * Resolve the active theme for today. Cached for 1 hour.
     */
    public static function getActive(): ?self
    {
        return Cache::remember('active_event_theme', 3600, function () {
            $today = now()->timezone('Asia/Makassar');
            $month = (int) $today->format('n');
            $day   = (int) $today->format('j');

            $themes = self::where('is_active', true)
                ->orderByDesc('priority')
                ->get();

            foreach ($themes as $theme) {
                if (self::isInRange(
                    $month, $day,
                    $theme->start_month, $theme->start_day,
                    $theme->end_month,   $theme->end_day
                )) {
                    return $theme;
                }
            }

            return null;
        });
    }

    /**
     * Check if a month/day falls within a range, supporting year-wrap (e.g. Dec–Jan).
     */
    public static function isInRange(
        int $month, int $day,
        int $startMonth, int $startDay,
        int $endMonth, int $endDay
    ): bool {
        $current = $month * 100 + $day;
        $start   = $startMonth * 100 + $startDay;
        $end     = $endMonth * 100 + $endDay;

        if ($start <= $end) {
            return $current >= $start && $current <= $end;
        }

        // Year-wrap (e.g. Dec 25 – Jan 5)
        return $current >= $start || $current <= $end;
    }

    /**
     * Return the countdown target date for the current year (or next year if already passed).
     */
    public function countdownTarget(): ?\Carbon\Carbon
    {
        if (! $this->show_countdown || ! $this->countdown_month || ! $this->countdown_day) {
            return null;
        }

        $now  = now()->timezone('Asia/Makassar');
        $year = (int) $now->format('Y');
        $date = \Carbon\Carbon::create($year, $this->countdown_month, $this->countdown_day, 0, 0, 0, 'Asia/Makassar');

        if ($date->isPast()) {
            $date->addYear();
        }

        return $date;
    }

    /** Flush the cached active theme (call when themes are updated). */
    public static function flushCache(): void
    {
        Cache::forget('active_event_theme');
    }

    /**
     * Wrap getActive() in try/catch so DB failures don't break every page.
     */
    public static function getActiveSafe(): ?self
    {
        try {
            return self::getActive();
        } catch (\Throwable) {
            return null;
        }
    }
}
