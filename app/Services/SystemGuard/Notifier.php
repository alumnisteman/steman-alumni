<?php

namespace App\Services\SystemGuard;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Notifier
{
    public static function send(string $message, string $level = 'info'): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (empty($token) || empty($chatId)) {
            Log::warning('SystemGuard Notifier: TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID not set.');
            return;
        }

        $emoji = match($level) {
            'critical' => '🚨',
            'warning'  => '⚠️',
            'success'  => '✅',
            default    => 'ℹ️',
        };

        $appName = config('app.name', 'STEMAN Alumni');
        $env     = config('app.env', 'production');
        $time    = now()->format('d M Y H:i:s');

        $text = "{$emoji} *{$appName}* [{$env}]\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . $message . "\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🕒 {$time}";

        try {
            Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('SystemGuard Notifier: Failed to send Telegram message — ' . $e->getMessage());
        }
    }
}
