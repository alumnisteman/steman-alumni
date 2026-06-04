<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Simple wrapper to send messages to Telegram.
 *
 * Usage:
 *   TelegramNotifier::send('Hello');
 */
class TelegramNotifier
{
    /**
     * Send a message via Telegram Bot API.
     *
     * @param string $message
     * @return void
     */
    public static function send(string $message): void
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId   = env('TELEGRAM_CHAT_ID');

        if (! $botToken || ! $chatId) {
            Log::warning('Telegram credentials missing – cannot send alert');
            return;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $client = new Client(['timeout' => 5]);
        try {
            $client->post($url, [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }
}
?>
