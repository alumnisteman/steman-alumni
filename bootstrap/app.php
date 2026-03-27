<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'alumni' => \App\Http\Middleware\AlumniMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->appendToGroup('web', 'throttle:global');
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
