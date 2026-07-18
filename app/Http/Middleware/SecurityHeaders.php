<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // HSTS (Strict-Transport-Security) - only set if connection is secure
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Setup Content Security Policy (CSP)
        $csp = $this->getCspDirective();
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    /**
     * Generate Content Security Policy directive.
     */
    protected function getCspDirective(): string
    {
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:",
            "img-src 'self' data: blob: https: http:",
            "media-src 'self' data: https: http:",
            "object-src 'none'",
            "frame-src 'self' https://maps.google.com https://www.google.com",
            "worker-src 'self' blob: https://www.gstatic.com",
            "child-src 'self' blob: https://www.gstatic.com",
        ];

        // If local development, allow Vite Dev Server HMR
        if (config('app.env') === 'local') {
            // Under local env, allow any local loopback/ports for assets & HMR to prevent browser matching issues
            $directives[1] .= ' http: https: ws: wss: http://localhost:* http://127.0.0.1:*';
            $directives[2] .= ' http: https: ws: wss: http://localhost:* http://127.0.0.1:*';
            $directives[] = "connect-src 'self' ws: wss: http: https: blob:";
        } else {
            $directives[] = "connect-src 'self' https: blob:";
        }

        return implode('; ', $directives);
    }
}
