<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status === 'pending') {
            // Check if they are accessing a blocked route. 
            // Only allow dashboard, profile edit, logout, and the pending view.
            $allowedRoutes = ['alumni.dashboard', 'pending.notice', 'logout', 'profile.edit', 'profile.update'];
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('pending.notice');
            }
        }
        return $next($request);
    }
}
