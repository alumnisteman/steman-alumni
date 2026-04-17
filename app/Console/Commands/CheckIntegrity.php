<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Exception;

class CheckIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steman:check-integrity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the health and integrity of core application components.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->header('STEMAN ALUMNI - INTEGRITY AUDIT');
        
        $results = [
            'Database' => $this->checkDatabase(),
            'Meilisearch' => $this->checkMeilisearch(),
            'Storage' => $this->checkStorage(),
            'Environment' => $this->checkEnvironment(),
        ];

        $this->newLine();
        if (in_array(false, $results, true)) {
            $this->error('!! SYSTEM INTEGRITY COMPROMISED !!');
            return 1;
        }

        $this->info('✓ ALL SYSTEMS OPERATIONAL');
        return 0;
    }

    private function header($text)
    {
        $this->newLine();
        $this->line('<bg=blue;fg=white> ' . $text . ' </>');
        $this->newLine();
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            $this->info('✓ Database: Connected');
            return true;
        } catch (Exception $e) {
            $this->error('✗ Database: Connection Failed - ' . $e->getMessage());
            return false;
        }
    }

    private function checkMeilisearch()
    {
        $host = config('scout.meilisearch.host', 'http://localhost:7700');
        $key = config('scout.meilisearch.key');

        try {
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $key])
                ->timeout(5)
                ->get($host . '/health');

            if ($response->successful()) {
                $this->info('✓ Meilisearch: Healthy');
                return true;
            }
            
            $this->warn('! Meilisearch: Reachable but returned status ' . $response->status());
            return false;
        } catch (Exception $e) {
            $this->error('✗ Meilisearch: Offline or Unreachable - ' . $e->getMessage());
            return false;
        }
    }

    private function checkStorage()
    {
        $paths = [
            storage_path('app/public'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
        ];

        $allWritable = true;
        foreach ($paths as $path) {
            if (!is_writable($path)) {
                $this->error('✗ Storage: Path is not writable - ' . $path);
                $allWritable = false;
            }
        }

        if ($allWritable) {
            $this->info('✓ Storage: All critical paths writable');
        }
        return $allWritable;
    }

    private function checkEnvironment()
    {
        $required = [
            'APP_KEY',
            'DB_DATABASE',
            'MEILISEARCH_KEY',
        ];

        $missing = [];
        foreach ($required as $key) {
            if (!env($key)) {
                $missing[] = $key;
            }
        }

        if (empty($missing)) {
            $this->info('✓ Environment: All vital variables set');
            return true;
        }

        $this->error('✗ Environment: Missing vital variables - ' . implode(', ', $missing));
        return false;
    }
}
