<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MetricsController extends Controller
{
    /**
     * Provide Prometheus-compatible metrics.
     */
    public function prometheus()
    {
        $metrics = [];

        // --- 1. CORE SYSTEM METRICS ---
        
        // Total Alumni
        $totalAlumni = User::where('role', 'alumni')->count();
        $metrics[] = "# HELP steman_alumni_total Total registered alumni";
        $metrics[] = "# TYPE steman_alumni_total gauge";
        $metrics[] = "steman_alumni_total {$totalAlumni}";

        // Online Alumni (approximate via activity)
        $onlineAlumni = User::online()->count();
        $metrics[] = "# HELP steman_alumni_online Total alumni currently online (active in last 30m)";
        $metrics[] = "# TYPE steman_alumni_online gauge";
        $metrics[] = "steman_alumni_online {$onlineAlumni}";

        // Queue Length
        $queueLength = DB::table('jobs')->count();
        $metrics[] = "# HELP steman_queue_length Number of jobs in queue";
        $metrics[] = "# TYPE steman_queue_length gauge";
        $metrics[] = "steman_queue_length {$queueLength}";

        // PHP Memory Usage
        $memoryUsed = round(memory_get_usage(true) / 1024 / 1024, 2);
        $metrics[] = "# HELP steman_memory_used_mb PHP Memory usage in MB";
        $metrics[] = "# TYPE steman_memory_used_mb gauge";
        $metrics[] = "steman_memory_used_mb {$memoryUsed}";

        // --- 2. SECURITY & SYSTEM GUARDIAN ---

        // Failed Login Attempts (from DB)
        $failedLogins = DB::table('activity_logs')->where('action', 'Login Failed')->count();
        $metrics[] = "# HELP steman_failed_logins_total Total failed login attempts recorded";
        $metrics[] = "# TYPE steman_failed_logins_total counter";
        $metrics[] = "steman_failed_logins_total {$failedLogins}";

        // Throttled Logins & Bot Detections (from Logs)
        $logFile = storage_path('logs/laravel.log');
        $throttledCount = 0;
        $botCount = 0;
        $healingCount = 0;
        $errorCount = 0;

        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $throttledCount = substr_count($logContent, 'Login throttled for IP:');
            $botCount = substr_count($logContent, 'Bot detected on login:');
            $healingCount = substr_count($logContent, 'Guardian: Fixed permissions') + substr_count($logContent, 'Guardian AI: Pattern detected');
            $errorCount = substr_count($logContent, 'production.ERROR:');
        }

        $metrics[] = "# HELP steman_throttled_logins_total Total login attempts blocked by rate limiter";
        $metrics[] = "# TYPE steman_throttled_logins_total counter";
        $metrics[] = "steman_throttled_logins_total {$throttledCount}";

        $metrics[] = "# HELP steman_bot_detections_total Total bots detected via honeypot";
        $metrics[] = "# TYPE steman_bot_detections_total counter";
        $metrics[] = "steman_bot_detections_total {$botCount}";

        $metrics[] = "# HELP steman_self_healing_events_total Total self-healing actions taken by Guardian";
        $metrics[] = "# TYPE steman_self_healing_events_total counter";
        $metrics[] = "steman_self_healing_events_total {$healingCount}";

        $metrics[] = "# HELP steman_http_errors_total Total application errors in logs";
        $metrics[] = "# TYPE steman_http_errors_total counter";
        $metrics[] = "steman_http_errors_total {$errorCount}";

        // --- 3. ENGAGEMENT & GAMIFICATION ---

        // Registration Funnel
        $pendingUsers = User::where('status', 'pending')->count();
        $approvedUsers = User::where('status', 'approved')->count();
        
        $metrics[] = "# HELP steman_registration_status Users by status";
        $metrics[] = "# TYPE steman_registration_status gauge";
        $metrics[] = "steman_registration_status{status=\"pending\"} {$pendingUsers}";
        $metrics[] = "steman_registration_status{status=\"approved\"} {$approvedUsers}";

        // Post Activity (Nostalgia Feed)
        $totalPosts = DB::table('posts')->count();
        $recentPosts = DB::table('posts')->where('created_at', '>=', now()->subHour())->count();

        $metrics[] = "# HELP steman_posts_total Total posts in nostalgia feed";
        $metrics[] = "# TYPE steman_posts_total counter";
        $metrics[] = "steman_posts_total {$totalPosts}";

        $metrics[] = "# HELP steman_posts_velocity_hour Posts created in the last hour";
        $metrics[] = "# TYPE steman_posts_velocity_hour gauge";
        $metrics[] = "steman_posts_velocity_hour {$recentPosts}";

        // Points & Leaderboard
        $totalPoints = User::sum('points');
        $metrics[] = "# HELP steman_points_total Sum of all points awarded to alumni";
        $metrics[] = "# TYPE steman_points_total gauge";
        $metrics[] = "steman_points_total {$totalPoints}";

        $topAlumni = User::where('role', 'alumni')->orderBy('points', 'desc')->take(5)->get();
        foreach ($topAlumni as $index => $user) {
            $rank = $index + 1;
            $safeName = addslashes($user->name);
            $metrics[] = "steman_top_alumni_points{rank=\"{$rank}\",name=\"{$safeName}\"} {$user->points}";
        }

        // --- 4. DONATION & TRANSPARENCY ---
        
        $totalDonationAmount = DB::table('donations')->where('status', 'verified')->sum('amount');
        $metrics[] = "# HELP steman_donations_verified_total_amount Sum of all verified donations";
        $metrics[] = "# TYPE steman_donations_verified_total_amount gauge";
        $metrics[] = "steman_donations_verified_total_amount {$totalDonationAmount}";

        $activeCampaigns = DB::table('donation_campaigns')->where('status', 'active')->get();
        foreach ($activeCampaigns as $campaign) {
            $safeTitle = addslashes($campaign->title);
            $percent = $campaign->goal_amount > 0 ? round(($campaign->current_amount / $campaign->goal_amount) * 100, 2) : 0;
            $metrics[] = "steman_campaign_progress_percent{title=\"{$safeTitle}\"} {$percent}";
            $metrics[] = "steman_campaign_collected_amount{title=\"{$safeTitle}\"} {$campaign->current_amount}";
        }

        // --- 5. SYSTEM PULSE (ARCHITECTURE HEALTH) ---
        // This mirrors the logic in SystemController@healthApi
        
        $dbName = config('database.connections.mysql.database');
        $dbSize = DB::select("SELECT SUM(data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName])[0]->size ?? 0;
        $metrics[] = "# HELP steman_db_size_bytes Total size of MySQL database in bytes";
        $metrics[] = "# TYPE steman_db_size_bytes gauge";
        $metrics[] = "steman_db_size_bytes {$dbSize}";

        $meiliDocs = 0;
        $meiliStatus = 0;
        try {
            $meiliHost = config('scout.meilisearch.host', 'http://steman_meilisearch:7700');
            $meiliKey = config('scout.meilisearch.key', 'stemanMasterKey123');
            $response = \Illuminate\Support\Facades\Http::withHeaders(['Authorization' => "Bearer {$meiliKey}"])
                ->timeout(2)
                ->get("{$meiliHost}/stats");
            
            if ($response->successful()) {
                $stats = $response->json();
                $meiliDocs = $stats['databaseSize'] ?? 0; // Use databaseSize or loop through indexes
                // Actually, let's sum documents across all indexes
                $meiliDocs = 0;
                if (isset($stats['indexes'])) {
                    foreach ($stats['indexes'] as $index) {
                        $meiliDocs += $index['numberOfDocuments'];
                    }
                }
                $meiliStatus = 1;
            }
        } catch (\Exception $e) {}

        $metrics[] = "# HELP steman_meili_documents_total Total documents indexed in Meilisearch";
        $metrics[] = "# TYPE steman_meili_documents_total gauge";
        $metrics[] = "steman_meili_documents_total {$meiliDocs}";

        $nodes = [
            'nginx' => 1, // Assume up if we are serving metrics
            'laravel' => 1,
            'mysql' => 0,
            'redis' => 0,
            'meilisearch' => $meiliStatus,
            'newsapi' => config('services.newsapi.key') ? 1 : 0,
            'rsshub' => 0
        ];

        // Check DB
        try {
            DB::connection()->getPdo();
            $nodes['mysql'] = 1;
        } catch (\Exception $e) {}

        // Check Redis
        try {
            \Illuminate\Support\Facades\Redis::connection()->ping();
            $nodes['redis'] = 1;
        } catch (\Exception $e) {}

        // Check RSSHub
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(2)->get('https://rsshub.app/');
            if ($res->status() < 500) $nodes['rsshub'] = 1;
        } catch (\Exception $e) {}

        // Check News API
        try {
            $newsKey = config('services.newsapi.key');
            if ($newsKey) {
                $newsRes = \Illuminate\Support\Facades\Http::timeout(2)->get("https://newsapi.org/v2/top-headlines?country=id&pageSize=1&apiKey={$newsKey}");
                if ($newsRes->successful()) $nodes['newsapi'] = 1;
            }
        } catch (\Exception $e) {}

        foreach ($nodes as $node => $status) {
            $metrics[] = "steman_node_status{node=\"{$node}\"} {$status}";
        }

        // --- 6. GEOGRAPHIC DISTRIBUTION ---
        $mapData = User::getMapAnalytics();
        $metrics[] = "# HELP steman_geo_distribution Alumni location count";
        $metrics[] = "# TYPE steman_geo_distribution gauge";
        $metrics[] = "steman_geo_distribution{region=\"national\"} {$mapData['nationalCount']}";
        $metrics[] = "steman_geo_distribution{region=\"international\"} {$mapData['internationalCount']}";

        // Top Cities
        $topCities = User::whereNotNull('city_name')
            ->select('city_name', DB::raw('count(*) as count'))
            ->groupBy('city_name')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        foreach ($topCities as $city) {
            $safeCity = addslashes($city->city_name);
            $metrics[] = "steman_alumni_by_city{city=\"{$safeCity}\"} {$city->count}";
        }

        return response(implode("\n", $metrics) . "\n", 200)
            ->header('Content-Type', 'text/plain; version=0.0.4');
    }
}
