<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OptimizeSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steman:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize database tables and clean up system logs/cache for performance.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting System Optimization...');

        // 1. Optimize Database Tables
        $this->info('Optimizing Database Tables...');
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $property = "Tables_in_{$dbName}";

        foreach ($tables as $table) {
            $tableName = $table->$property;
            $this->line(" - Optimizing {$tableName}...");
            DB::statement("OPTIMIZE TABLE {$tableName}");
        }

        // 2. Clean Up Old Logs (Activity Logs older than 30 days)
        $this->info('Cleaning up old activity logs...');
        $deleted = DB::table('activity_logs')->where('created_at', '<', now()->subDays(30))->delete();
        $this->info(" - Removed {$deleted} old log entries.");

        // 3. Clear Cache
        $this->info('Clearing application cache...');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('config:clear');

        // 4. Recalculate Totals (Optional maintenance)
        $this->info('Recalculating search indexes...');
        $this->call('scout:flush', ['model' => 'App\Models\User']);
        $this->call('scout:import', ['model' => 'App\Models\User']);

        $this->info('System Optimization Complete!');
        Log::info('System Pulse: Optimization command executed successfully.');
        
        return Command::SUCCESS;
    }
}
