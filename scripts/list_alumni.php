<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::where('role', 'alumni')->get(['id', 'name', 'city_name', 'email']);

echo "ID | Name | City | Email\n";
echo str_repeat("-", 50) . "\n";
foreach ($users as $user) {
    echo "{$user->id} | {$user->name} | {$user->city_name} | {$user->email}\n";
}
