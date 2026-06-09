<?php

namespace App\Services\SystemGuard;

use Illuminate\Support\Facades\Log;

class Notifier
{
    public static function send(string $message, string $level = 'info'): void
    {
        $token  = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

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
            $ch = curl_init("https://api.telegram.org/bot{$token}/sendMessage");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query([
                    'chat_id'    => $chatId,
                    'text'       => substr($text, 0, 4096),
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
                Log::error('SystemGuard Notifier: curl error — ' . $errMsg);
            }
        } catch (\Throwable $e) {
            Log::error('SystemGuard Notifier: Failed to send Telegram message — ' . $e->getMessage());
        }
    }
}
