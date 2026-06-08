<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk mengirim notifikasi Telegram secara asinkron via queue.
 * Menggantikan @file_get_contents sinkron yang memblokir response.
 */
class SendTelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 15;

    public function __construct(
        private readonly string $message,
        private readonly string $parseMode = 'Markdown'
    ) {}

    public function handle(): void
    {
        $token  = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            Log::debug('SendTelegramNotification: token/chat_id tidak dikonfigurasi, skip.');
            return;
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query([
                    'chat_id'    => $chatId,
                    'text'       => substr($this->message, 0, 4000),
                    'parse_mode' => $this->parseMode,
                ]),
                'timeout' => 10,
            ],
        ]);

        $result = @file_get_contents($url, false, $ctx);

        if ($result === false) {
            Log::warning('SendTelegramNotification: Gagal mengirim ke Telegram.');
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendTelegramNotification job gagal: ' . $e->getMessage());
    }
}
