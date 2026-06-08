<?php

namespace App\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware; // Laravel 8/9
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Using '*', we trust all proxies (e.g., Cloudflare, Docker network).
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;

    /**
     * Handle an incoming request.
     * Guard against null client IP — health checks and bots manipulating
     * headers can produce a null IP which causes IpUtils::checkIp4() to crash
     * with "Argument #2 ($ip) must be of type string, null given".
     */
    public function handle(Request $request, \Closure $next)
    {
        if (empty($request->server->get('REMOTE_ADDR'))) {
            $request->server->set('REMOTE_ADDR', '127.0.0.1');
        }

        return parent::handle($request, $next);
    }
}
