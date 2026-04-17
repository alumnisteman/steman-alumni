<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ad;
use App\Services\AdsImageService;

try {
    echo "Starting Save Diagnostic...\n";
    $ad = Ad::find(1);
    $service = new AdsImageService();
    
    echo "Ad Title: " . $ad->title . "\n";
    echo "Current Image: " . $ad->getRawOriginal('image') . "\n";
    
    // Test auto-generation logic
    echo "Testing autoGenerateMobile...\n";
    $mobileImage = $service->autoGenerateMobile($ad->getRawOriginal('image'), $ad->position);
    
    if ($mobileImage) {
        echo "SUCCESS: Auto-generated mobile image: " . $mobileImage . "\n";
    } else {
        echo "FAILED: autoGenerateMobile returned null\n";
    }
    
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . " - " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "TRACE:\n" . $e->getTraceAsString() . "\n";
}
