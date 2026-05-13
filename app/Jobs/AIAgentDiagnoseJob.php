<?php

namespace App\Jobs;

use App\Services\AutonomousAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AIAgentDiagnoseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $errorLog;
    protected $filePath;
    protected $lineNumber;

    /**
     * Create a new job instance.
     */
    public function __construct($errorLog, $filePath, $lineNumber)
    {
        $this->errorLog = $errorLog;
        $this->filePath = $filePath;
        $this->lineNumber = $lineNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(AutonomousAgent $agent): void
    {
        Log::info("AIAgentDiagnoseJob: Starting diagnosis...");
        
        $healed = $agent->diagnoseAndHeal($this->errorLog, $this->filePath, $this->lineNumber);

        if ($healed) {
            $this->notifyTelegram("🤖 *Steman Autonomous Agent*\n\n✅ Sistem berhasil melakukan self-healing!\n\n*Error Teratasi:* `" . substr($this->errorLog, 0, 150) . "...`\n*Lokasi:* `{$this->filePath}`");
        } else {
            // FALLBACK: If AI fails to heal specific code, perform a global system stabilization
            Log::warning("AIAgentDiagnoseJob: AI failed to heal code. Triggering SystemAutoFix fallback...");
            
            try {
                \Illuminate\Support\Facades\Artisan::call('system:autofix', ['--force' => true]);
                $this->notifyTelegram("🤖 *Steman Autonomous Agent*\n\n⚠️ AI gagal memperbaiki kode secara spesifik, namun *SystemAutoFix* telah dijalankan untuk menstabilkan sistem (clear cache, audit DB, optimize).\n\n*Original Error:* `" . substr($this->errorLog, 0, 150) . "...`\n*Status:* Sistem telah distabilkan.");
            } catch (\Exception $e) {
                Log::error("AIAgentDiagnoseJob: SystemAutoFix fallback failed: " . $e->getMessage());
                $this->notifyTelegram("🤖 *Steman Autonomous Agent*\n\n❌ Gagal melakukan self-healing dan stabilisasi otomatis.\n\n*Error:* `" . substr($this->errorLog, 0, 150) . "...`\n\n*Tindakan:* Mohon bantuan teknis manual SEGERA.");
            }
        }
    }

    private function notifyTelegram($message)
    {
        if (config('app.env') === 'production') {
            $telegramToken = env('TELEGRAM_BOT_TOKEN');
            $telegramChatId = env('TELEGRAM_CHAT_ID');
            
            if ($telegramToken && $telegramChatId) {
                $ctx = stream_context_create(['http' => ['timeout' => 5]]);
                @file_get_contents("https://api.telegram.org/bot{$telegramToken}/sendMessage?" . http_build_query([
                    'chat_id' => $telegramChatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown'
                ]), false, $ctx);
            }
        }
    }
}
