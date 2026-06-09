<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Simple wrapper to send messages to Telegram.
 *
 * Usage:
 *   TelegramNotifier::send('Hello');
 */
class TelegramNotifier
{
    public static function send(string $message): void
    {
        $botToken = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
        $chatId   = config('services.telegram.chat_id')   ?: env('TELEGRAM_CHAT_ID');

        if (!$botToken || !$chatId) {
            Log::warning('Telegram credentials missing – cannot send alert');
            return;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query([
                    'chat_id'    => $chatId,
                    'text'       => substr($message, 0, 4096),
                    'parse_mode' => 'Markdown',
                ]),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);
            $response = curl_exec($ch);
            $errNo    = curl_errno($ch);
            $errMsg   = curl_error($ch);
            curl_close($ch);

            if ($errNo) {
                Log::error('TelegramNotifier curl error: ' . $errMsg);
            }
        } catch (\Throwable $e) {
            Log::error('TelegramNotifier::send failed: ' . $e->getMessage());
        }
    }
}
