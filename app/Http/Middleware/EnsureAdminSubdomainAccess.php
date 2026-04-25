<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSubdomainAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $adminSubdomain = 'admin.' . parse_url(config('app.url'), PHP_URL_HOST);

        // If we are on the admin subdomain
        if ($host === $adminSubdomain) {
            
            // Allow public assets or specific paths if needed (optional)
            if ($request->is('img-opt/*') || $request->is('api/v1/guardian/*')) {
                return $next($request);
            }

            // If user is logged in
            if (Auth::check()) {
                // If user is NOT admin or editor, kick them out to the main domain
                if (!in_array(Auth::user()->role, ['admin', 'editor'])) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect(config('app.url'))
                        ->with('error', 'Akses ke Panel Admin ditolak. Silakan login melalui portal utama.');
                }
            }
            
            // If they are visiting the root of admin subdomain while not logged in, 
            // they will be caught by the 'auth' or 'role' middleware on the routes anyway.
        }

        return $next($request);
    }
}
