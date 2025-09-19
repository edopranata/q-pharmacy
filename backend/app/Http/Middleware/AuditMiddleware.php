<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        // Only log API requests for authenticated users
        if (Auth::check() && $request->is('api/*')) {
            $this->logApiRequest($request, $response, $startTime);
        }
        
        return $response;
    }
    
    /**
     * Log API request details
     */
    private function logApiRequest(Request $request, Response $response, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2); // in milliseconds
        
        // Only log certain actions or if response indicates an important operation
        if ($this->shouldLogRequest($request, $response)) {
            $action = $this->getActionFromRequest($request);
            
            AuditLogger::log(
                $action,
                null,
                null,
                [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'status_code' => $response->getStatusCode(),
                    'duration_ms' => $duration,
                    'request_size' => strlen($request->getContent()),
                    'response_size' => strlen($response->getContent()),
                ],
                $this->getRequestDescription($request, $response)
            );
        }
    }
    
    /**
     * Determine if request should be logged
     */
    private function shouldLogRequest(Request $request, Response $response): bool
    {
        // Log all write operations (POST, PUT, PATCH, DELETE)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return true;
        }
        
        // Log failed requests
        if ($response->getStatusCode() >= 400) {
            return true;
        }
        
        // Log specific read operations (reports, exports, etc.)
        if ($request->is('api/*/export*') || $request->is('api/reports/*')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get action name from request
     */
    private function getActionFromRequest(Request $request): string
    {
        $method = strtolower($request->method());
        $path = $request->path();
        
        if (str_contains($path, 'export')) {
            return 'exported';
        }
        
        if (str_contains($path, 'import')) {
            return 'imported';
        }
        
        return match ($method) {
            'post' => 'api_created',
            'put', 'patch' => 'api_updated',
            'delete' => 'api_deleted',
            'get' => 'api_accessed',
            default => 'api_request',
        };
    }
    
    /**
     * Get human-readable description
     */
    private function getRequestDescription(Request $request, Response $response): string
    {
        $method = $request->method();
        $path = $request->path();
        $status = $response->getStatusCode();
        
        return "{$method} {$path} - Status: {$status}";
    }
}
