<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComingSoon
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $mode = setting('coming_soon_mode', 'off');
        } catch (\Throwable $e) {
            return $next($request);
        }

        if ($mode !== 'on') {
            return $next($request);
        }

        $host = $request->getHost();
        if (str_starts_with($host, 'admin.')) {
            return $next($request);
        }

        $bypassPaths = ['up', 'login', 'logout', 'register', 'password/*', 'coming-soon'];
        foreach ($bypassPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        if ($request->is('admin*') || $request->is('api/*')) {
            return $next($request);
        }

        try {
            if (auth()->check() && in_array(auth()->user()->role, ['admin', 'editor'])) {
                return $next($request);
            }
        } catch (\Throwable $e) {
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Portal sedang dalam pemeliharaan. Segera hadir!'], 503);
        }

        try {
            return response()->view('coming_soon', [], 503);
        } catch (\Throwable $e) {
            return response('<html><body style="background:#0f172a;color:#fff;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;"><div style="text-align:center"><h1 style="color:#ffcc00;font-size:3rem;font-weight:900;">COMING SOON</h1><p style="color:#94a3b8;">Portal sedang dalam pemeliharaan. Segera hadir!</p></div></body></html>', 503);
        }
    }
}
