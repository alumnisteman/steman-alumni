<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ad;

try {
    echo "Bootstrapping complete...\n";
    $ad = Ad::find(1);
    if (!$ad) {
        die("Ad not found\n");
    }
    
    echo "Ad found: " . $ad->title . "\n";
    echo "Attempting to render view 'admin.ads.edit'...\n";
    
    // We need to set up a request context for view rendering to work (for @csrf, etc)
    $request = Illuminate\Http\Request::create('/admin/ads/1', 'GET');
    $app->instance('request', $request);
    
    $html = view("admin.ads.edit", ["ad" => $ad])->render();
    echo "SUCCESS: View rendered successfully! (HTML length: " . strlen($html) . ")\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . " - " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "TRACE:\n" . $e->getTraceAsString() . "\n";
}
