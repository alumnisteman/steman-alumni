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
            $ctx = stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'content' => http_build_query([
                        'chat_id'    => $chatId,
                        'text'       => substr($message, 0, 4096),
                        'parse_mode' => 'Markdown',
                    ]),
                    'timeout' => 5,
                    'ignore_errors' => true,
                ],
            ]);
            @file_get_contents($url, false, $ctx);
        } catch (\Throwable $e) {
            Log::error('TelegramNotifier::send failed: ' . $e->getMessage());
        }
    }
}
