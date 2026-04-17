<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ad;
use App\Services\AdsImageService;
use Illuminate\Support\Facades\Storage;

try {
    echo "Starting Update Logic Simulation...\n";
    $ad = Ad::find(1);
    $service = new AdsImageService();
    
    // Mocking the flow in AdController@update
    $data = [
        'title' => 'Test Update ' . time(),
        'position' => $ad->position,
    ];
    
    echo "Simulating auto-regeneration of mobile image...\n";
    // This is line 114 in AdController
    $data['mobile_image'] = $service->autoGenerateMobile($ad->getRawOriginal('image'), $ad->position);
    
    echo "Data to update: " . json_encode($data) . "\n";
    
    $ad->update($data);
    echo "SUCCESS: Ad updated successfully!\n";
    
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . " - " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "TRACE:\n" . $e->getTraceAsString() . "\n";
}
