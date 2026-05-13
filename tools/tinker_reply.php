<?php
// Script to generate AI reply for contact message
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AIService;
use App\Models\ContactMessage;

$aiService = app(AIService::class);
$message = ContactMessage::find(2);

if (!$message) {
    die("FAILED: Message not found");
}

$prompt = "Seseorang bernama kurnianto mengirim pesan: 'testes tes tes tes tes tes testestes'. Tolong buatkan balasan singkat dan sangat profesional sebagai admin alumni untuk mengonfirmasi bahwa pesan mereka sudah diterima.";

$reply = $aiService->ask($prompt);

if ($reply) {
    $message->update([
        'reply_content' => $reply,
        'replied_at' => now(),
        'is_read' => true
    ]);
    echo "SUCCESS: " . $reply;
} else {
    echo "FAILED: AI service returned null";
}
