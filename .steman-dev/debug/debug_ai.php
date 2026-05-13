<?php

define('LARAVEL_START', microtime(true));

require dirname(__DIR__).'/vendor/autoload.php';

$app = require_once dirname(__DIR__).'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- AIService Debug ---\n";
$ai = new \App\Services\AIService();

echo "Config Gemini Key: " . (config('services.gemini.api_key') ? 'EXISTS' : 'MISSING') . "\n";
echo "Config OpenRouter Key: " . (config('services.openrouter.api_key') ? 'EXISTS' : 'MISSING') . "\n";

echo "\nTesting ask()...\n";
$result = $ai->ask("Hello, are you active? Reply with 'ACTIVE' only.", 0.1);
echo "Result: " . ($result ?? 'NULL') . "\n";

echo "\nTesting checkHealth()...\n";
$health = $ai->checkHealth();
print_r($health);
