<?php
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

try {
    echo "Starting Diagnostic...\n";
    
    echo "1. Checking User count: " . \App\Models\User::count() . "\n";
    echo "2. Checking Business count: " . \App\Models\Business::count() . "\n";
    
    echo "3. Testing Bakery DB Connection...\n";
    try {
        $count = \Illuminate\Support\Facades\DB::connection('mysql')->table('bakery_app.admins')->count();
        echo "Bakery Admins: $count\n";
    } catch (\Exception $e) {
        echo "Bakery DB Error: " . $e->getMessage() . "\n";
    }
    
    echo "4. Checking Disk Space...\n";
    $storagePath = storage_path('app/public');
    echo "Storage Path: $storagePath\n";
    $storageFree = @disk_free_space($storagePath);
    $storageTotal = @disk_total_space($storagePath);
    echo "Total: $storageTotal, Free: $storageFree\n";
    
    if ($storageTotal == 0) {
        echo "ALERT: Storage Total is 0! (Division by zero risk detected)\n";
    }
    
    echo "5. Checking Storage Integrity...\n";
    try {
        $files = \Illuminate\Support\Facades\Storage::disk('public')->files('businesses');
        echo "Physical files in businesses: " . count($files) . "\n";
    } catch (\Exception $e) {
        echo "Storage Error: " . $e->getMessage() . "\n";
    }
    
    echo "6. Checking Activity Feed...\n";
    $recent = \App\Models\User::latest()->take(5)->get();
    echo "Recent Activities Count: " . $recent->count() . "\n";
    
    echo "Diagnostic Complete.\n";
} catch (\Throwable $e) {
    echo "FATAL DIAGNOSTIC ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
