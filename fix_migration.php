<?php
require "/var/www/html/vendor/autoload.php";
$app = require_once "/var/www/html/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Mark migration as ran
try {
    DB::table('migrations')->insert([
        'migration' => '2026_04_06_000000_create_social_tables',
        'batch' => 99
    ]);
    echo "Migration marked as ran.\n";
} catch (\Exception $e) {
    echo "Already exists or error: " . $e->getMessage() . "\n";
}

// Clear caches
Artisan::call('cache:clear');
echo "Cache cleared.\n";
Artisan::call('route:clear');
echo "Route cache cleared.\n";
Artisan::call('view:clear');
echo "View cache cleared.\n";
Artisan::call('config:clear');
echo "Config cache cleared.\n";

// Storage link
try {
    Artisan::call('storage:link');
    echo "Storage linked.\n";
} catch (\Exception $e) {
    echo "Storage link: " . $e->getMessage() . "\n";
}

echo "\nALL DONE!\n";
