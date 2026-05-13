<?php
// Fix paths to point to project root from storage directory
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$fakePatterns = ['London', 'Paris', 'New York', 'Tokyo', 'Berlin', 'Sydney', 'Fake', 'Test', 'Jakarta', 'Makassar', 'Sorong', 'Manado', 'Alumni '];

$users = User::where('role', 'alumni')->get();
$toDelete = [];

foreach ($users as $user) {
    $isFake = false;
    foreach ($fakePatterns as $pattern) {
        if (stripos($user->city_name, $pattern) !== false || stripos($user->name, $pattern) !== false) {
            $isFake = true;
            break;
        }
    }
    
    if (str_contains($user->email, 'example.com') || str_contains($user->email, 'test.com')) {
        $isFake = true;
    }

    if ($isFake) {
        $toDelete[] = $user;
    }
}

if (empty($toDelete)) {
    echo "No fake alumni found.\n";
    exit;
}

echo "Found " . count($toDelete) . " potentially fake alumni:\n";
foreach ($toDelete as $user) {
    echo "- ID: {$user->id} | Name: {$user->name} | City: {$user->city_name} | Email: {$user->email}\n";
}

// Perform deletion
foreach ($toDelete as $user) {
    $user->delete();
}
echo "Successfully deleted " . count($toDelete) . " fake alumni.\n";
