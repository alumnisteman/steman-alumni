<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $ttl = 3600): Response
    {
        // Only cache GET requests and only for guest users (not logged in)
        if (!$request->isMethod('GET') || auth()->check()) {
            return $next($request);
        }

        // Create a unique cache key based on the full URL including query string
        $key = 'response_cache_' . md5($request->fullUrl());

        if (Cache::has($key)) {
            $content = Cache::get($key);
            return response($content)->header('X-Steman-Cache', 'Hit');
        }

        $response = $next($request);

        // Only cache successful HTML responses
        if ($response->isSuccessful() && str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            // Append a small HTML comment to show it was cached
            $content = $response->getContent() . "\n<!-- Cached at " . now()->toIso8601String() . " -->";
            Cache::put($key, $content, $ttl);
            
            // Set the content back to the response
            $response->setContent($content);
            $response->header('X-Steman-Cache', 'Miss');
        }

        return $response;
    }
}
