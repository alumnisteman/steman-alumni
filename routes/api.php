<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AlumniController;

Route::domain('api.alumni-steman.my.id')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::get('/', function() {
            return response()->json([
                'name' => 'Steman Alumni API',
                'version' => '1.0.0',
                'status' => 'operational'
            ]);
        });
        
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

            // FIX MEDIUM: Kirim notifikasi Telegram via queue (asinkron)
            // Sebelumnya menggunakan @file_get_contents sinkron yang menambah latency response
            if (config('app.env') === 'production') {
                $blockedUri        = $report['csp-report']['blocked-uri'] ?? 'Unknown';
                $violatedDirective = $report['csp-report']['violated-directive'] ?? 'Unknown';
                $documentUri       = $report['csp-report']['document-uri'] ?? 'Unknown';

                $teleMsg = "🛡️ *CSP VIOLATION* 🛡️\n\n*Blocked URI:* {$blockedUri}\n*Directive:* {$violatedDirective}\n*Page:* {$documentUri}";

                \App\Jobs\SendTelegramNotification::dispatch($teleMsg);
            }
        }
        return response()->json(['success' => true]);
    });
});
