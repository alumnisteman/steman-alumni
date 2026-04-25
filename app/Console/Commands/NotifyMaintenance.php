<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class NotifyMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steman:notify-maintenance {status} {message?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send maintenance/deployment notification to Telegram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->argument('status');
        $customMessage = $this->argument('message');
        
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            $this->error('Telegram configuration missing in .env');
            return 1;
        }

        $emoji = $status === 'success' ? '✅' : '❌';
        $title = $status === 'success' ? '*MAINTENANCE BERHASIL*' : '*MAINTENANCE GAGAL*';
        $body = $customMessage ?? ($status === 'success' ? 'Deployment dan optimasi sistem telah selesai dengan sempurna.' : 'Terjadi kesalahan saat proses maintenance. Mohon periksa log.');

        $text = "🛠️ {$title}\n\n{$emoji} {$body}\n\n📅 " . now()->format('d M Y H:i:s');

        try {
            Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
            $this->info('Notification sent to Telegram.');
        } catch (\Exception $e) {
            $this->error('Failed to send Telegram notification: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
