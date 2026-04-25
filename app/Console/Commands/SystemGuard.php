<?php

namespace App\Console\Commands;

use App\Services\SystemGuard\Fixer;
use App\Services\SystemGuard\HealthChecker;
use App\Services\SystemGuard\Notifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
            $msg .= "🚨 *Butuh Perhatian Manual:*\n" . implode("\n", array_map(fn($i) => "  • `{$i}`", $unfixed)) . "\n\n";
        }
        $msg .= "Total: " . count($results) . " checks | " . count($issues) . " issues found.";

        Notifier::send($msg, empty($unfixed) ? 'warning' : 'critical');

        Log::warning('SystemGuard: Issues detected — ' . implode(', ', $issues));

        return empty($unfixed) ? 0 : 1;
    }
}
