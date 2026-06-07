<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComingSoon
{
    public function handle(Request $request, Closure $next): Response
    {
        if (setting('coming_soon_mode', 'off') !== 'on') {
            return $next($request);
        }

        $bypass = [
            '/up',
            '/login',
            '/logout',
            '/register',
            '/password/*',
            '/coming-soon',
        ];

        foreach ($bypass as $path) {
            if ($request->is(ltrim($path, '/'))) {
                return $next($request);
            }
        }

        if ($request->is('admin*') || $request->is('api/*')) {
            return $next($request);
        }

        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'editor'])) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Portal sedang dalam pemeliharaan. Segera hadir!'], 503);
        }

        return response()->view('coming_soon', [], 503);
    }
}
