<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('email', 'admin@steman.ac.id')->first();
if ($user) {
    echo "USER FOUND: {$user->name}, Role: {$user->role}, Status: {$user->status}\n";
} else {
    echo "USER NOT FOUND\n";
}
