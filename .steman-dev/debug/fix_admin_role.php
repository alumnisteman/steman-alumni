<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('email', 'admin@steman.ac.id')->first();
if ($user) {
    echo "Updating role for {$user->email} from {$user->role} to admin...\n";
    $user->role = 'admin';
    $user->save();
    echo "DONE.\n";
} else {
    echo "USER NOT FOUND\n";
}
