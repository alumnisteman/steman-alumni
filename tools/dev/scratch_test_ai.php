<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ai = new \App\Services\AIService();
echo "Testing AI Service...\n";
$health = $ai->checkHealth();
print_r($health);

echo "\nTesting Ask DeepSeek/Llama via OpenRouter:\n";
$response = $ai->ask("Siapa namamu dan model apa kamu?", 0.7);
echo "Response: " . ($response ?? "FAILED") . "\n";
