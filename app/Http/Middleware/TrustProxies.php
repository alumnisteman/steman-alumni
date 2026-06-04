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
}
