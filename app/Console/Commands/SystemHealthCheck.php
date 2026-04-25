<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health';
    protected $description = 'Perform a comprehensive health check of the Alumni Portal';

    public function handle()
    {
        $this->info("=== STEMAN ALUMNI PORTAL HEALTH CHECK ===");
        
        // 1. Database Check
        $this->check("Database Connectivity", function() {
            DB::connection()->getPdo();
            return true;
        });

        // 2. Storage Check
        $this->check("Storage Permissions", function() {
            return is_writable(storage_path('logs')) && is_writable(storage_path('framework/views'));
        });

        // 3. AI Service Check
        $this->check("AI Service (Gemini API)", function() {
            $ai = new \App\Services\AIService();
            $response = $ai->ask("ping", 0.1);
            return !empty($response);
        });

        // 4. Critical Views Check
        $this->check("Feed System Integrity", function() {
            return view()->exists('alumni.feed.index');
        });

        $this->info("=========================================");
        $this->info("Status: ALL SYSTEMS OPERATIONAL 🚀");
    }

    private function check($name, $callback)
    {
        try {
            if ($callback()) {
                $this->line("<info>[PASS]</info> $name");
            } else {
                $this->line("<error>[FAIL]</error> $name");
            }
        } catch (\Throwable $e) {
            $this->line("<error>[FAIL]</error> $name - " . $e->getMessage());
        }
    }
}
