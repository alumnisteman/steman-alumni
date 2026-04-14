<?php
// Comprehensive Gemini Debugger
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = config('services.gemini.api_key');
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$apiKey";

echo "--- DEBUG START ---\n";
echo "Testing API Key ending in: " . substr($apiKey, -5) . "\n";

$response = Http::withHeaders(['Content-Type' => 'application/json'])
    ->timeout(15)
    ->post($url, [
        'contents' => [['parts' => [['text' => 'Hi']]]]
    ]);

echo "Status Code: " . $response->status() . "\n";
echo "Raw Body: " . $response->body() . "\n";
echo "--- DEBUG END ---\n";
