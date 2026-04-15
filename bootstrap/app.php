<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
            } catch (\Throwable $fatalInherited) {
                // Last resort: standard system error log
                error_log("Laravel Critical Error: " . $e->getMessage());
            }
        });
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->guest('/login')->with('error', 'Sesi login Anda telah berakhir karena batas waktu tidak ada aktivitas. Silakan masuk kembali.');
        });
    })->create();
