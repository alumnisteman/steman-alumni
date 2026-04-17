<?php
// Standalone diagnostic script - Corrected Bootstrapping
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Manually boot the application for Eloquent access
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ad;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "DIAGNOSTIC START\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "DB Connection: " . DB::connection()->getDatabaseName() . "\n";

try {
    echo "\nRAW SQL ADS COUNT:\n";
    $rawCount = DB::table('ads')->count();
    echo "Total Ads in DB: " . $rawCount . "\n";
    
    $allAds = DB::table('ads')->get();
    foreach ($allAds as $ad) {
        echo "- ID: {$ad->id}, Pos: {$ad->position}, Active: {$ad->is_active}, Start: {$ad->start_date}, End: {$ad->end_date}\n";
    }

    echo "\nELOQUENT ACTIVE SCOPE:\n";
    $count = Ad::active()->count();
    echo "Active Ads Count: " . $count . "\n";
    
    $ads = Ad::active()->get();
    foreach ($ads as $ad) {
        echo "- ID: {$ad->id}, Title: {$ad->title}, Position: {$ad->position}\n";
    }

    echo "\nCACHE STATE:\n";
    echo "Cache Key 'active_ads_v7' exists: " . (Cache::has('active_ads_v7') ? 'YES' : 'NO') . "\n";
    if (Cache::has('active_ads_v7')) {
        $cached = Cache::get('active_ads_v7');
        echo "Cached items count: " . $cached->count() . "\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\nDIAGNOSTIC END\n";
