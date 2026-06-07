<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $token;
    protected string $chatId;

    public function __construct()
    {
        $this->token  = config('services.telegram.bot_token') ?: (string) env('TELEGRAM_BOT_TOKEN');
        $this->chatId = config('services.telegram.chat_id')   ?: (string) env('TELEGRAM_CHAT_ID');
    }

    public function sendMessage(string $text): bool
    {
        if (!$this->token || !$this->chatId) {
            Log::warning('TelegramService: credentials missing');
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
            $ctx = stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'content' => http_build_query([
                        'chat_id'    => $this->chatId,
                        'text'       => substr($text, 0, 4096),
                        'parse_mode' => 'HTML',
                    ]),
                    'timeout' => 5,
                    'ignore_errors' => true,
                ],
            ]);
            $result = @file_get_contents($url, false, $ctx);
            if ($result === false) {
                Log::warning('TelegramService: HTTP request failed');
                return false;
            }
            $decoded = json_decode($result, true);
            return isset($decoded['ok']) && $decoded['ok'] === true;
        } catch (\Throwable $e) {
            Log::error('TelegramService::sendMessage failed: ' . $e->getMessage());
            return false;
        }
    }
}
