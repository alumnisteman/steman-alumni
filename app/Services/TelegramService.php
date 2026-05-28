<?php

namespace App\Services;

use GuzzleHttp\Client;

class TelegramService
{
    protected $token;
    protected $chatId;
    protected $client;

    public function __construct()
    {
        $this->token  = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
        $this->client = new Client(['base_uri' => "https://api.telegram.org");
    }

    public function sendMessage(string $text): bool
    {
        if (!$this->token || !$this->chatId) {
            return false;
        }
        $response = $this->client->post("/bot{$this->token}/sendMessage", [
            'form_params' => [
                'chat_id' => $this->chatId,
                'text'    => $text,
                'parse_mode' => 'HTML',
            ],
        ]);
        return $response->getStatusCode() === 200;
    }
}
