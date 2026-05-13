<?php

/**
 * PRODUCTION SMOKE TEST SCRIPT
 * Run this after deployment: docker exec <container> php -f scripts/verify-deployment.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$errors = [];
$checks = [
    'Critical Tables' => function() use (&$errors) {
        $tables = ['users', 'news', 'success_stories', 'migrations'];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) $errors[] = "Table missing: $table";
        }
    },
    'Critical Routes' => function() use (&$errors) {
        $routes = [
            'admin.dashboard', 
            'alumni.dashboard', 
            'admin.success-stories.index',
            'success-stories.show'
        ];
        foreach ($routes as $route) {
            if (!Route::has($route)) $errors[] = "Route missing: $route";
        }
    },
    'Database Connectivity' => function() use (&$errors) {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $errors[] = "DB Connection failed: " . $e->getMessage();
        }
    }
];

echo "=== STEMAN ALUMNI DEPLOYMENT VERIFIER ===\n";
foreach ($checks as $name => $check) {
    echo "Checking $name... ";
    $check();
    echo "OK\n";
}

if (!empty($errors)) {
    echo "\n!!! DEPLOYMENT ISSUES DETECTED !!!\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
    exit(1);
}

echo "\n--- ALL SYSTEMS GO ---\n";
exit(0);
