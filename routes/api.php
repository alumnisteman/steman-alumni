<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AlumniController;

Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Public or Protected Auth Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        
        // Alumni Routes
        Route::get('/alumni', [AlumniController::class, 'index']);
        Route::get('/alumni/{identifier}', [AlumniController::class, 'show']);
    });
});

Route::post('/csp-report', function (Request $request) {
    $report = json_decode($request->getContent(), true);
    if ($report) {
        \Illuminate\Support\Facades\Log::warning('CSP Violation:', $report);
        if (config('app.env') === 'production') {
            $telegramToken = env('TELEGRAM_BOT_TOKEN');
            $telegramChatId = env('TELEGRAM_CHAT_ID');
            if ($telegramToken && $telegramChatId) {
                $blockedUri = $report['csp-report']['blocked-uri'] ?? 'Unknown';
                $violatedDirective = $report['csp-report']['violated-directive'] ?? 'Unknown';
                $documentUri = $report['csp-report']['document-uri'] ?? 'Unknown';
                
                $teleMsg = "🛡️ *CSP VIOLATION* 🛡️\n\n*Blocked URI:* $blockedUri\n*Directive:* $violatedDirective\n*Page:* $documentUri";
                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                @file_get_contents("https://api.telegram.org/bot{$telegramToken}/sendMessage?" . http_build_query([
                    'chat_id' => $telegramChatId,
                    'text' => $teleMsg,
                    'parse_mode' => 'Markdown'
                ]), false, $ctx);
            }
        }
    }
    return response()->json(['success' => true]);
});
