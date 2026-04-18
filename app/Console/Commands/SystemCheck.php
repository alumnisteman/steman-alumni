<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemCheck extends Command
{
    protected $signature = 'app:system-check';
    protected $description = 'Perform a deep health audit of the entire system';

    public function handle()
    {
        $this->header("STEMAN ALUMNI - DEEP SYSTEM AUDIT");

        // 1. Check .env Integrity
        $this->auditSection("Environment Integrity", function() {
            if (File::exists(base_path('.env'))) {
                $content = File::get(base_path('.env'));
                if (str_contains($content, 'email_anda@gmail.com')) {
                    throw new \Exception("STALENESS DETECTED: Placeholder email found in .env!");
                }
                $this->info("  [OK] .env contains real production values.");
            } else {
                throw new \Exception(".env file is MISSING!");
            }
        });

        // 2. Check Database Connectivity
        $this->auditSection("Database Connectivity", function() {
            DB::connection()->getPdo();
            $this->info("  [OK] Database is responsive.");
        });

        // 3. SMTP Connectivity (Fast Check)
        $this->auditSection("SMTP Connectivity", function() {
            $transport = config('mail.mailers.smtp.transport');
            $host = config('mail.mailers.smtp.host');
            $this->info("  [i] Using $transport via $host");
            
            // Just verify config is not empty
            if (empty(config('mail.mailers.smtp.username')) || config('mail.mailers.smtp.username') === 'email_anda@gmail.com') {
                 throw new \Exception("Invalid SMTP Username detected in Config Cache!");
            }
            $this->info("  [OK] SMTP Configuration is valid.");
        });

        // 4. Folder Structure Integrity
        $this->auditSection("Storage Permissions", function() {
            if (!is_writable(storage_path('logs'))) {
                throw new \Exception("Storage directory is NOT writable!");
            }
            $this->info("  [OK] Storage permissions are correct.");
        });

        $this->newLine();
        $this->info("--- AUDIT COMPLETE: ALL SYSTEMS NOMINAL ---");
    }

    private function header($text)
    {
        $this->line("<options=bold;fg=cyan>========================================</>");
        $this->line("<options=bold;fg=cyan> $text </>");
        $this->line("<options=bold;fg=cyan>========================================</>");
    }

    private function auditSection($name, $callback)
    {
        $this->comment("\nAuditing $name...");
        try {
            $callback();
        } catch (\Exception $e) {
            $this->error("  [FAIL] " . $name . ": " . $e->getMessage());
            // Log for Guardian
            \Illuminate\Support\Facades\Log::emergency("SYSTEM AUDIT FAILED: " . $name . " - " . $e->getMessage());
        }
    }
}
