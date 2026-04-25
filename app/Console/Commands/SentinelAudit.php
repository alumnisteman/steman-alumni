<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SentinelService;

class SentinelAudit extends Command
{
    protected $signature = 'sentinel:audit {--fix : Automatically apply fixes}';
    protected $description = 'Perform a system-wide audit for performance and accessibility issues';

    public function handle(SentinelService $sentinel)
    {
        $this->info('Starting Sentinel System Audit...');
        
        $report = $sentinel->performAudit();

        if (count($report['issues']) > 0) {
            $this->warn('Audit found ' . count($report['issues']) . ' potential issues:');
            foreach ($report['issues'] as $issue) {
                $this->line(" - [!] {$issue}");
            }
            $this->info("Fixes applied: {$report['fixes_applied']}");
        } else {
            $this->info('--- ALL SYSTEMS NOMINAL ---');
        }

        return count($report['issues']) === 0 ? 0 : 1;
    }
}
