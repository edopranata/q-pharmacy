<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\UserActivity;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk tracking aktivitas pengguna
 * 
 * Middleware ini akan:
 * 1. Update last_activity timestamp
 * 2. Track page visits dan API calls
 * 3. Monitor session activity
 * 4. Log suspicious activities
 */
class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only track authenticated users
        if (Auth::check()) {
            $this->trackUserActivity($request);
        }
        
        return $response;
    }
    
    /**
     * Track user activity
     */
    private function trackUserActivity(Request $request): void
    {
        $user = Auth::user();
        $now = now();
        
        // Update last activity in cache (for real-time tracking)
        $this->updateLastActivity($user->id, $now);
        
        // Track detailed activity (optional, for analytics)
        if ($this->shouldTrackDetailedActivity($request)) {
            $this->logDetailedActivity($user, $request, $now);
        }
        
        // Update database periodically (to avoid too many DB writes)
        $this->updateDatabaseActivity($user, $now);
        
        // Check for suspicious activity
        $this->checkSuspiciousActivity($user, $request);
    }
    
    /**
     * Update last activity in cache
     */
    private function updateLastActivity(int $userId, $timestamp): void
    {
        $key = "user_last_activity_{$userId}";
        Cache::put($key, $timestamp, 3600); // Cache for 1 hour
    }
    
    /**
     * Check if we should track detailed activity
     */
    private function shouldTrackDetailedActivity(Request $request): bool
    {
        // Skip tracking for certain routes
        $skipRoutes = [
            'api/auth/user', // User profile endpoint (called frequently)
            'api/heartbeat', // Health check
            'api/notifications/unread-count', // Notification polling
        ];
        
        $currentRoute = $request->path();
        
        foreach ($skipRoutes as $skipRoute) {
            if (str_contains($currentRoute, $skipRoute)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Log detailed activity for analytics
     */
    private function logDetailedActivity($user, Request $request, $timestamp): void
    {
        try {
            UserActivity::create([
                'user_id' => $user->id,
                'action' => $this->getActionFromRequest($request),
                'route' => $request->path(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => $timestamp,
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the request
            Log::warning('Failed to log user activity', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update database activity periodically
     */
    private function updateDatabaseActivity($user, $timestamp): void
    {
        $lastUpdateKey = "user_db_update_{$user->id}";
        $lastUpdate = Cache::get($lastUpdateKey);
        
        // Only update database every 5 minutes to reduce DB load
        if (!$lastUpdate || $lastUpdate->diffInMinutes($timestamp) >= 5) {
            try {
                $user->update([
                    'last_activity' => $timestamp,
                    'updated_at' => $timestamp
                ]);
                
                Cache::put($lastUpdateKey, $timestamp, 600); // Cache for 10 minutes
            } catch (\Exception $e) {
                Log::warning('Failed to update user last_activity', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Check for suspicious activity
     */
    private function checkSuspiciousActivity($user, Request $request): void
    {
        $userId = $user->id;
        $currentIp = $request->ip();
        $currentUserAgent = $request->userAgent();
        
        // Check for IP address changes
        $lastIpKey = "user_last_ip_{$userId}";
        $lastIp = Cache::get($lastIpKey);
        
        if ($lastIp && $lastIp !== $currentIp) {
            $this->logSuspiciousActivity($user, 'ip_change', [
                'old_ip' => $lastIp,
                'new_ip' => $currentIp,
                'user_agent' => $currentUserAgent
            ]);
        }
        
        Cache::put($lastIpKey, $currentIp, 86400); // Cache for 24 hours
        
        // Check for user agent changes
        $lastUserAgentKey = "user_last_ua_{$userId}";
        $lastUserAgent = Cache::get($lastUserAgentKey);
        
        if ($lastUserAgent && $lastUserAgent !== $currentUserAgent) {
            $this->logSuspiciousActivity($user, 'user_agent_change', [
                'old_user_agent' => $lastUserAgent,
                'new_user_agent' => $currentUserAgent,
                'ip_address' => $currentIp
            ]);
        }
        
        Cache::put($lastUserAgentKey, $currentUserAgent, 86400); // Cache for 24 hours
        
        // Check for rapid requests (potential bot activity)
        $this->checkRapidRequests($userId, $request);
    }
    
    /**
     * Check for rapid requests that might indicate bot activity
     */
    private function checkRapidRequests(int $userId, Request $request): void
    {
        $key = "user_request_count_{$userId}";
        $requests = Cache::get($key, []);
        $now = time();
        
        // Remove requests older than 1 minute
        $requests = array_filter($requests, function($timestamp) use ($now) {
            return ($now - $timestamp) < 60;
        });
        
        // Add current request
        $requests[] = $now;
        
        // Check if too many requests in short time
        if (count($requests) > 100) { // More than 100 requests per minute
            $this->logSuspiciousActivity(Auth::user(), 'rapid_requests', [
                'request_count' => count($requests),
                'time_window' => '1_minute',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }
        
        Cache::put($key, $requests, 120); // Cache for 2 minutes
    }
    
    /**
     * Log suspicious activity
     */
    private function logSuspiciousActivity($user, string $type, array $details): void
    {
        Log::warning('Suspicious user activity detected', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'activity_type' => $type,
            'details' => $details,
            'timestamp' => now()
        ]);
        
        // You can also send notifications to admins here
        // event(new SuspiciousActivityDetected($user, $type, $details));
    }
    
    /**
     * Get action description from request
     */
    private function getActionFromRequest(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();
        
        // Map common patterns to readable actions
        $actionMap = [
            'GET /api/users' => 'view_users_list',
            'POST /api/users' => 'create_user',
            'PUT /api/users/' => 'update_user',
            'DELETE /api/users/' => 'delete_user',
            'GET /api/products' => 'view_products_list',
            'POST /api/products' => 'create_product',
            'GET /api/sales' => 'view_sales_list',
            'POST /api/sales' => 'create_sale',
            'GET /api/dashboard' => 'view_dashboard',
        ];
        
        foreach ($actionMap as $pattern => $action) {
            if (str_contains($pattern, $path) || str_starts_with($path, str_replace('/', '', explode(' ', $pattern)[1]))) {
                return $action;
            }
        }
        
        // Default action format
        return strtolower($method) . '_' . str_replace(['/', '-'], '_', $path);
    }
}