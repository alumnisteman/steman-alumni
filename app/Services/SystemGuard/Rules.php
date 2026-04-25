<?php

namespace App\Services\SystemGuard;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Rules — Smart retry, circuit breaker, and cooldown system.
 *
 * Circuit Breaker States:
 *   CLOSED  → Normal, attempts allowed
 *   OPEN    → Too many failures, block all attempts (circuit is "open"/tripped)
 *   HALF    → Testing recovery after cooldown
 */
class Rules
{
    private const MAX_RETRIES      = 3;
    private const COOLDOWN_SECONDS = 120;   // 2 min between retries
    private const OPEN_THRESHOLD   = 5;     // failures before circuit opens
    private const OPEN_DURATION    = 600;   // circuit stays open for 10 min

    private const CACHE_PREFIX = 'system_guard_circuit_';

    // ─── Circuit Breaker ───────────────────────────────────────────────

    public static function canHandle(string $issue): bool
    {
        $state = self::getState($issue);

        if ($state === 'OPEN') {
            // Check if open-duration has passed → move to HALF-OPEN
            $openedAt = Cache::get(self::CACHE_PREFIX . $issue . '_opened_at', 0);
            if ((time() - $openedAt) >= self::OPEN_DURATION) {
                self::setState($issue, 'HALF');
                Log::info("SystemGuard Circuit [{$issue}]: HALF-OPEN — testing recovery.");
                return true;
            }
            return false; // Still open
        }

        // Enforce cooldown between consecutive retry attempts
        $lastAttempt = Cache::get(self::CACHE_PREFIX . $issue . '_last', 0);
        if ((time() - $lastAttempt) < self::COOLDOWN_SECONDS) {
            return false;
        }

        // Enforce max retries cap (for current open cycle)
        $retries = Cache::get(self::CACHE_PREFIX . $issue . '_retries', 0);
        if ($retries >= self::MAX_RETRIES) {
            self::trip($issue);
            return false;
        }

        return true;
    }

    public static function record(string $issue): void
    {
        $retries = Cache::get(self::CACHE_PREFIX . $issue . '_retries', 0);
        Cache::put(self::CACHE_PREFIX . $issue . '_retries', $retries + 1, now()->addHour());
        Cache::put(self::CACHE_PREFIX . $issue . '_last', time(), now()->addHour());

        // Track global failures for anomaly detection
        $failures = Cache::get(self::CACHE_PREFIX . $issue . '_total_failures', 0);
        Cache::put(self::CACHE_PREFIX . $issue . '_total_failures', $failures + 1, now()->addDay());

        // Trip circuit if total failures exceed threshold
        if (($retries + 1) >= self::OPEN_THRESHOLD) {
            self::trip($issue);
        }
    }

    public static function reset(string $issue): void
    {
        // CLOSED → reset all counters (successful fix)
        Cache::forget(self::CACHE_PREFIX . $issue . '_retries');
        Cache::forget(self::CACHE_PREFIX . $issue . '_last');
        Cache::forget(self::CACHE_PREFIX . $issue . '_opened_at');
        self::setState($issue, 'CLOSED');
        Log::info("SystemGuard Circuit [{$issue}]: CLOSED — issue resolved.");
    }

    public static function trip(string $issue): void
    {
        self::setState($issue, 'OPEN');
        Cache::put(self::CACHE_PREFIX . $issue . '_opened_at', time(), now()->addHour());
        Log::warning("SystemGuard Circuit [{$issue}]: OPENED — too many failures. Cooling down " . self::OPEN_DURATION . "s.");
    }

    public static function getState(string $issue): string
    {
        return Cache::get(self::CACHE_PREFIX . $issue . '_state', 'CLOSED');
    }

    private static function setState(string $issue, string $state): void
    {
        Cache::put(self::CACHE_PREFIX . $issue . '_state', $state, now()->addDay());
    }

    // ─── Anomaly Detection Helpers ──────────────────────────────────────

    public static function getTotalFailures(string $issue): int
    {
        return Cache::get(self::CACHE_PREFIX . $issue . '_total_failures', 0);
    }

    public static function getAllStats(): array
    {
        $issues = [
            'db_down', 'redis_down', 'queue_overload', 'meili_down',
            'disk_low', 'storage_broken', 'log_bloated',
            'session_domain', 'captcha_patch', 'nginx_down',
            'audit_broken', 'route_mismatch',
        ];

        $stats = [];
        foreach ($issues as $issue) {
            $stats[$issue] = [
                'state'          => self::getState($issue),
                'retries'        => Cache::get(self::CACHE_PREFIX . $issue . '_retries', 0),
                'total_failures' => self::getTotalFailures($issue),
                'last_attempt'   => Cache::get(self::CACHE_PREFIX . $issue . '_last', null),
            ];
        }
        return $stats;
    }
}
