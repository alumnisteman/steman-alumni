<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\News;

$news = News::where('slug', 'siaran-pers-terkait-kejadian-pengrusakan-fasilitas-acara-reuni-akbar-steman-ternate-oUmnG')->first();
if ($news) {
    echo "ID: " . $news->id . "\n";
    echo "CONTENT:\n" . $news->content . "\n";
} else {
    echo "News not found\n";
}
