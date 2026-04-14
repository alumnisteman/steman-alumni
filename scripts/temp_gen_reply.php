<?php
// Script to generate AI reply for contact message
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AIService;
$aiService = app(AIService::class);

$prompt = "Seseorang bernama kurnianto mengirim pesan uji coba: 'testes tes tes tes tes tes testestes'. Tolong berikan balasan yang sangat singkat, ramah, dan profesional sebagai admin portal alumni untuk mengonfirmasi bahwa sistem pesan mereka sudah berfungsi dengan baik.";

echo $aiService->ask($prompt);
