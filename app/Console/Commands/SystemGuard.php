<?php

namespace App\Console\Commands;

use App\Services\SystemGuard\Fixer;
use App\Services\SystemGuard\HealthChecker;
use App\Services\SystemGuard\Notifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SystemGuard extends Command
{
    protected $signature = 'system:guard {--report : Send a full status report to Telegram even if healthy}';
    protected $description = 'Auto-healing system guard: detects issues, fixes them, and notifies via Telegram';

    public function handle(): int
    {
        $this->line('');
        $this->line('<bg=blue;fg=white> 🛡 SYSTEM GUARD — STEMAN ALUMNI </> ');
        $this->line('');

        $checker = new HealthChecker();
        $issues  = $checker->run();
        $results = $checker->getResults();

        // Display results
        foreach ($results as $check => $status) {
            if ($status === 'OK') {
                $this->line("  <info>✓</info>  {$check}");
            } else {
                $this->line("  <error>✗</error>  {$check}: {$status}");
            }
        }

        $this->line('');

        if (empty($issues)) {
            $this->info('✅ ALL SYSTEMS OPERATIONAL — No issues found.');

            if ($this->option('report')) {
                $checks = count($results);
                Notifier::send(
                    "✅ *Semua Sistem Berjalan Normal*\n"
                    . "{$checks}/{$checks} checks passed.\n"
                    . "Tidak ada tindakan yang diperlukan.",
                    'success'
                );
            }

            return 0;
        }

        // Fix each issue
        $fixed   = [];
        $unfixed = [];

        foreach ($issues as $issue) {
            $this->warn("  → Fixing: {$issue}");
            $result = Fixer::handle($issue);

            if ($result) {
                $fixed[] = $issue;
                $this->info("    ✓ Fixed: {$issue}");
            } else {
                $unfixed[] = $issue;
                $this->error("    ✗ Could not auto-fix: {$issue} (needs manual attention)");
            }
        }

        $this->line('');
        $this->line("  Summary: " . count($fixed) . " fixed, " . count($unfixed) . " requires manual action.");

        // Send combined Telegram report
        $msg = "🔍 *System Guard Report*\n\n";
        if (!empty($fixed)) {
            $msg .= "✅ *Auto-Fixed:*\n" . implode("\n", array_map(fn($i) => "  • `{$i}`", $fixed)) . "\n\n";
        }
        if (!empty($unfixed)) {
            // Filter issues to avoid spam (throttling)
            $notifiableUnfixed = array_filter($unfixed, function($issue) {
                if ($issue === 'ai_offline') {
                    $cacheKey = 'system_guard:alert_throttled:ai_offline';
                    if (Cache::has($cacheKey)) {
                        return false; // Skip this issue for Telegram report
                    }
                    // Lock for 1 hour
                    Cache::put($cacheKey, true, 3600);
                }
                return true;
            });

            if (!empty($notifiableUnfixed)) {
                $msg .= "🚨 *Butuh Perhatian Manual:*\n" . implode("\n", array_map(fn($i) => "  • `{$i}`", $notifiableUnfixed)) . "\n\n";
            } else if (count($unfixed) > 0) {
                $msg .= "⏳ *Masalah Berlanjut (Throttled):*\n  • `ai_offline` (Sedang dipantau, cek log server)\n\n";
            }
        }
        $msg .= "Total: " . count($results) . " checks | " . count($issues) . " issues found.";

        // Only send if there are new things to report or if specifically requested via --report
        if (!empty($fixed) || !empty($notifiableUnfixed) || $this->option('report')) {
            Notifier::send($msg, empty($unfixed) ? 'warning' : 'critical');
        }

        Log::warning('SystemGuard: Issues detected — ' . implode(', ', $issues));

        // Always return 0 so the scheduler doesn't treat this as a FATAL ERROR 500.
        // We already sent the proper critical/warning alert to Telegram via Notifier::send() above.
        return 0;
    }
}
