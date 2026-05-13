<?php
require '/var/www/steman-alumni/vendor/autoload.php';
$app = require_once '/var/www/steman-alumni/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$viewsPath = resource_path('views');
$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsPath));
foreach ($files as $file) {
    if ($file->isDir()) continue;
    if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) !== 'php') continue;
    $content = file_get_contents($file->getPathname());
    preg_match_all('/route\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
    foreach ($matches[1] as $routeName) {
        if (!\Illuminate\Support\Facades\Route::has($routeName)) {
            echo "Missing route: ". $routeName . " in " . $file->getPathname() . "\n";
        }
    }
}
