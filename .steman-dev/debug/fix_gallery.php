<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Gallery;

echo "--- Fixing Gallery Paths (V3) ---\n";

// Update EVERY photo that has a broken path
$broken = Gallery::where('type', 'photo')
    ->where(function($q) {
        $q->where('file_path', '/storage')
          ->orWhere('file_path', 'storage')
          ->orWhere('file_path', '/storage/')
          ->orWhere('file_path', '');
    })->get();

echo "Found " . $broken->count() . " broken records.\n";

$i = 0;
$files = ['/storage/gallery/1777025578.webp', '/storage/gallery/1777026318.webp'];

foreach ($broken as $b) {
    $newPath = $files[$i % 2];
    echo "Updating ID {$b->id} ({$b->title}) to {$newPath}\n";
    $b->file_path = $newPath;
    $b->status = 'published';
    $b->save();
    $i++;
}

echo "DONE.\n";
