<?php

namespace App\Services;

class ContentModerationService
{
    /**
     * Common Indonesian and local Ternate forbidden words.
     * This list can be moved to database or settings in the future.
     */
    protected static $blacklist = [
        // Standard Indonesian Profanity
        'anjing', 'babi', 'monyet', 'bangsat', 'brengsek', 'tai', 'kontol', 'memek', 'jembut', 'peler', 'itil', 'ngentot', 'asu', 'bajingan',
        'perek', 'jablay', 'lonte', 'pelacur', 'bejat', 'tolol', 'goblok', 'bego', 'idiot',
        
        // SARA / Hate Speech
        'kafir', 'sesat', 'cina', 'pki', 'komunis', 'bencong', 'bencong', 'homo', 'gay', 'lesbi', 'bencong',
        
        // Local Ternate / North Maluku Profanity & Insults
        'dogel', 'mek', 'tai kancu', 'cukimay', 'polo', 'boso', 'ngone', 'boltu', 'lofo', 'soat', 'kancu', 'muka lodo', 'puki', 'pukimai'
    ];

    /**
     * Check if the text contains any blacklisted words.
     * Returns true if clean, false if contains forbidden words.
     */
    public static function isClean(string $text): bool
    {
        if (empty($text)) return true;

        $text = strtolower($text);

        foreach (self::$blacklist as $word) {
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
        foreach (self::$blacklist as $word) {
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
        foreach (self::$blacklist as $word) {
            $replacement = str_repeat('*', strlen($word));
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $text = preg_replace($pattern, $replacement, $text);
        }
        return $text;
    }
}
