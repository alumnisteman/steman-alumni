<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function abort;

class AdminDomainGuard
{
    /**
     * Handle an incoming request.
     *
     * aborts with 403 if the host is not the admin subdomain.
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedHost = 'admin.alumni-steman.my.id';
        // Exempt login, logout, and password reset routes from host check
        $exemptPaths = ['/login', '/logout', '/password/reset', '/password/email'];
        // Use Laravel's request methods; they exist at runtime.
        $path = '/' . ltrim($request->path(), '/');
        if (!in_array($path, $exemptPaths) && $request->getHost() !== $allowedHost) {
            abort(403, 'Forbidden: access only allowed from admin subdomain.');
        }
        return $next($request);
    }
}
