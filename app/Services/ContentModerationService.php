<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ContentModerationService
{
    /**
     * Get blacklist words from database with caching.
     */
    protected static function getBlacklist(): array
    {
        return Cache::remember('content_moderation_blacklist', 3600, function () {
            $words = DB::table('content_moderation_words')
                ->where('is_active', true)
                ->pluck('word')
                ->toArray();
            
            // Fallback to hardcoded list if database is empty
            if (empty($words)) {
                return self::getDefaultBlacklist();
            }
            
            return $words;
        });
    }

    /**
     * Default blacklist words as fallback.
     */
    protected static function getDefaultBlacklist(): array
    {
        return [
            // Standard Indonesian Profanity
            'anjing', 'babi', 'monyet', 'bangsat', 'brengsek', 'tai', 'kontol', 'memek', 'jembut', 'peler', 'itil', 'ngentot', 'asu', 'bajingan',
            'perek', 'jablay', 'lonte', 'pelacur', 'bejat', 'tolol', 'goblok', 'bego', 'idiot',
            
            // SARA / Hate Speech
            'kafir', 'sesat', 'cina', 'pki', 'komunis', 'bencong', 'bencong', 'homo', 'gay', 'lesbi', 'bencong',
            
            // Local Ternate / North Maluku Profanity & Insults
            'dogel', 'mek', 'tai kancu', 'cukimay', 'polo', 'boso', 'ngone', 'boltu', 'lofo', 'soat', 'kancu', 'muka lodo', 'puki', 'pukimai'
        ];
    }

    /**
     * Clear the blacklist cache (call after updating database).
     */
    public static function clearCache(): void
    {
        Cache::forget('content_moderation_blacklist');
    }

    /**
     * Check if the text contains any blacklisted words.
     * Returns true if clean, false if contains forbidden words.
     */
    public static function isClean(string $text): bool
    {
        if (empty($text)) return true;

        $text = strtolower($text);
        $blacklist = self::getBlacklist();

        foreach ($blacklist as $word) {
            // Using regex with word boundaries to avoid false positives (e.g., "pakaian" matches "kai")
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            if (preg_match($pattern, $text)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Identify which forbidden word was found (for logging/admin info).
     */
    public static function getViolation(string $text): ?string
    {
        $text = strtolower($text);
        $blacklist = self::getBlacklist();
        
        foreach ($blacklist as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            if (preg_match($pattern, $text)) {
                return $word;
            }
        }
        return null;
    }

    /**
     * Sanitize text by replacing bad words with asterisks (Optional UI treatment).
     */
    public static function mask(string $text): string
    {
        $text = strtolower($text);
        $blacklist = self::getBlacklist();
        
        foreach ($blacklist as $word) {
            $replacement = str_repeat('*', strlen($word));
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $text = preg_replace($pattern, $replacement, $text);
        }
        return $text;
    }
}
