<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    // Nettoyer tout output buffer
        if (ob_get_length() > 0) {
            ob_clean();
        }
        
        $response = $next($request);
        
        // Forcer le Content-Type pour les réponses JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $response->headers->set('Content-Type', 'application/json');
        }
        
        return $response;
    }
}
