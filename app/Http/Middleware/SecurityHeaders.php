<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN'); // Protected from being framed
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Allowed external sources for Media Embeds (TikTok, YouTube)
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://www.tiktok.com https://sf-tb-sg.ibytedtos.com https://lf16-tiktok-web.ttwstatic.com; ";
        $csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; ";
        $csp .= "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com; ";
        $csp .= "img-src 'self' data: https://ui-avatars.com https://*.tiktokcdn.com https://*.ibytedtos.com; ";
        $csp .= "video-src 'self' blob: https://*.tiktokcdn.com https://*.youtube.com; ";
        $csp .= "frame-src 'self' https://www.youtube.com https://www.tiktok.com; ";
        $csp .= "connect-src 'self' https://*.tiktok.com https://*.ibytedtos.com;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
