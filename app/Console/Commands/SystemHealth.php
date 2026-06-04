<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SystemHealth extends Command
{
    protected $signature = 'system:health';
    protected $description = 'Check DB, Redis, Meilisearch, Grafana and send Telegram alert on failure';

    public function handle()
    {
        $issues = [];

        // Database
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $issues[] = "DB connection failed: " . $e->getMessage();
        }

        // Redis
        try {
            Redis::ping();
        } catch (\Exception $e) {
            $issues[] = "Redis ping failed: " . $e->getMessage();
        }

        // Meilisearch
        try {
            $client = new Client(['timeout' => 2]);
            $client->get(config('scout.meilisearch.host') . '/health');
        } catch (\Exception $e) {
            $issues[] = "Meilisearch unreachable: " . $e->getMessage();
        }

        // Grafana (via internal service URL)
        try {
            $client = new Client(['timeout' => 2]);
            $client->get('http://grafana:3000/api/health');
        } catch (\Exception $e) {
            $issues[] = "Grafana unreachable: " . $e->getMessage();
        }

        if (!empty($issues)) {
            $msg = "⚠️ *System Health Alert*\n" . implode("\n", $issues);
            $this->sendTelegram($msg);
            $this->error('Issues detected – notification sent');
            Log::warning('System health issues: ' . implode(' | ', $issues));
        } else {
            $this->info('All services healthy');
        }

        return 0;
    }

    protected function sendTelegram(string $message)
    {
        \App\Services\TelegramNotifier::send($message);
    }

}
?>
