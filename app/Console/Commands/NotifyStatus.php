<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steman:notify-status {task} {status} {detail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi status sistem (Backup/Maintenance) ke email Admin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = $this->argument('task');
        $status = $this->argument('status');
        $detail = $this->argument('detail');

        $adminEmail = config('mail.from.address');
        $appName = config('app.name');

        $content = "
        <h2>Laporan Status Sistem - {$appName}</h2>
        <p><strong>Tugas:</strong> {$task}</p>
        <p><strong>Status:</strong> {$status}</p>
        <p><strong>Detail:</strong><br>{$detail}</p>
        <hr>
        <p>Laporan ini dikirim otomatis oleh sistem pada " . date('Y-m-d H:i:s') . "</p>
        ";

        try {
            Mail::html($content, function ($message) use ($adminEmail, $task, $status) {
                $message->to($adminEmail)
                    ->subject("📢 [{$status}] Laporan {$task} - STEMAN Alumni Portal");
            });

            $this->info('Notifikasi email berhasil dikirim ke ' . $adminEmail);
        } catch (\Exception $e) {
            $this->error('Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
