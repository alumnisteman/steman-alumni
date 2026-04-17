<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

if (!function_exists('redirect')) {
    function redirect($to = null, $status = 302, $headers = [], $secure = null) {
        if (is_null($to)) {
            return app('redirect');
        }
        return app('redirect')->to($to, $status, $headers, $secure);
    }
}

if (!function_exists('back')) {
    function back($status = 302, $headers = [], $fallback = false) {
        return app('redirect')->back($status, $headers, $fallback);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'alumni' => \App\Http\Middleware\AlumniMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'verified_alumni' => \App\Http\Middleware\EnsureUserIsVerified::class,
        ]);
        // Exempt /logout from CSRF: forcing a logout is not a harmful CSRF attack
        $middleware->validateCsrfTokens(except: ['/logout']);
        $middleware->appendToGroup('web', 'throttle:global');
        $middleware->trustProxies(at: '*');
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            // Master Emergency Logger: Captures fatal context safely
            try {
                // Safely try to get user ID, fallback to Guest if session/auth is not ready
                $user = 'Guest';
                try {
                    if (app()->bound('auth') && app('auth')->guard()->check()) {
                        $user = app('auth')->guard()->id();
                    }
                } catch (\Throwable $authIgnored) {}

                $msg = sprintf("[%s] [EMERGENCY] User:%s | URL:%s\nMessage: %s\nStack Trace:\n%s\n%s\n", 
                    date('Y-m-d H:i:s'), 
                    $user, 
                    request()->fullUrl(), 
                    $e->getMessage(),
                    $e->getTraceAsString(),
                    str_repeat('-', 80)
                );
                @file_put_contents(storage_path('logs/emergency_fatal.log'), $msg, FILE_APPEND);

                // --- PROACTIVE TELEGRAM WEBHOOK ---
                if (config('app.env') === 'production') {
                    $telegramToken = env('TELEGRAM_BOT_TOKEN');
                    $telegramChatId = env('TELEGRAM_CHAT_ID');
                    
                    if ($telegramToken && $telegramChatId) {
                        $teleMsg = "🚨 *FATAL ERROR 500* 🚨\n\n*Env:* " . config('app.env') . "\n*URL:* " . request()->fullUrl() . "\n*User ID:* " . $user . "\n*Error:* " . $e->getMessage();
                        
                        // Fire and forget using stream context with short timeout so it doesn't block response
                        $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                        @file_get_contents("https://api.telegram.org/bot{$telegramToken}/sendMessage?" . http_build_query([
                            'chat_id' => $telegramChatId,
                            'text' => substr($teleMsg, 0, 4000),
                            'parse_mode' => 'Markdown'
                        ]), false, $ctx);
                    }
                }
            } catch (\Throwable $fatalInherited) {
                // Last resort: standard system error log
                error_log("Laravel Critical Error: " . $e->getMessage());
            }
        });
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->guest('/login')->with('error', 'Sesi login Anda telah berakhir karena batas waktu tidak ada aktivitas. Silakan masuk kembali.');
        });
        
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // API Fallback Shield
            if ($request->wantsJson() || $request->is('api/*')) {
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface || 
                    $e instanceof \Illuminate\Validation\ValidationException || 
                    $e instanceof \Illuminate\Auth\AuthenticationException) {
                    return null; // Normal API routing for known errors
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Sistem sedang memulihkan diri dari gangguan.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Self-healing diaktifkan. Harap coba beberapa saat lagi.'
                ], 500);
            }

            // Web Fallback Shield (Only for fatal 500s in Production)
            if (config('app.env') === 'production' && !config('app.debug')) {
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface || 
                    $e instanceof \Illuminate\Validation\ValidationException || 
                    $e instanceof \Illuminate\Auth\AuthenticationException ||
                    $e instanceof \Illuminate\Session\TokenMismatchException) {
                    return null; // Let standard error pages/redirects handle it
                }
                
                return response()->view('errors.500', [], 500);
            }
        });
    })->create();
