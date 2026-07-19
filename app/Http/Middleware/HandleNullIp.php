<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleNullIp
{
    /**
     * Handle an incoming request.
     *
     * If the client IP is null (which can happen with some proxy configurations),
     * we set a default value to avoid Symfony\Component\HttpFoundation\IpUtils errors.
     */
    public function handle(Request $request, Closure $next)
    {
        if (is_null($request->ip())) {
            // Set a safe fallback IP address.
            $request->server->set('REMOTE_ADDR', '127.0.0.1');
        }
        return $next($request);
    }
}
