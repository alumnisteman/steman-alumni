<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'admin@steman.ac.id')->first();
if ($user) {
    echo "Resetting password for {$user->email}...\n";
    $user->password = Hash::make('Admin@1234');
    $user->role = 'admin'; // Double check role
    $user->status = 'approved';
    $user->save();
    echo "DONE.\n";
} else {
    echo "USER NOT FOUND\n";
}
