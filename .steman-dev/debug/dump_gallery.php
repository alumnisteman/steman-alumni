<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Gallery;

$photos = Gallery::where('type', 'photo')->latest()->take(4)->get();
foreach ($photos as $p) {
    echo "ID: {$p->id}, Title: '{$p->title}', Path: '{$p->file_path}', Status: {$p->status}\n";
}
