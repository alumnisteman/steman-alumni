<?php

namespace App\Services;

class PrivacyService
{
    /**
     * Mask an email address
     * Example: help@steman.id -> h***@steman.id
     */
    public static function maskEmail(?string $email): ?string
    {
        if (empty($email)) return null;
        
        $parts = explode('@', $email);
        $local = $parts[0];
        $domain = $parts[1] ?? '';
        
        $maskedLocal = substr($local, 0, 1) . str_repeat('*', 3);
        
        return $maskedLocal . '@' . $domain;
    }

    /**
     * Mask a phone number
     * Example: 08123456789 -> 08****789
     */
    public static function maskPhone(?string $phone): ?string
    {
        if (empty($phone)) return null;

        // Strip non-numeric
        $clean = preg_replace('/[^0-9]/', '', $phone);
        $length = strlen($clean);

        if ($length < 7) return $phone;

        return substr($clean, 0, 2) . str_repeat('*', 4) . substr($clean, -3);
    }
}
