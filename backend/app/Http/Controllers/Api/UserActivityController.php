<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\BaseResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
/**
 * Controller untuk mengelola data aktivitas pengguna
 */
class UserActivityController extends Controller
{
    /**
     * Get current user's activity summary
     */
    public function myActivity(Request $request)
    {
        $user = Auth::user();
        $days = $request->input('days', 30);
        
        // Get activity stats
        $stats = UserActivity::getStatsForUser($user->id, $days);
        
        // Get recent activities
        $recentActivities = UserActivity::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'action' => $activity->formatted_action,
                    'route' => $activity->route,
                    'method' => $activity->method,
                    'ip_address' => $activity->ip_address,
                    'browser' => $activity->browser,
                    'platform' => $activity->platform,
                    'created_at' => $activity->created_at,
                    'created_at_human' => $activity->created_at->diffForHumans(),
                ];
            });
        
        // Get last activity from cache (real-time)
        $lastActivityCache = Cache::get("user_last_activity_{$user->id}");
        
        return BaseResponseService::success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'last_login' => $user->last_login,
                'last_login_human' => $user->last_login?->diffForHumans(),
                'last_activity' => $user->last_activity,
                'last_activity_human' => $user->last_activity?->diffForHumans(),
                'last_activity_realtime' => $lastActivityCache,
                'last_activity_realtime_human' => $lastActivityCache?->diffForHumans(),
            ],
            'stats' => $stats,
            'recent_activities' => $recentActivities,
            'period_days' => $days
        ], 'User activity retrieved successfully');
    }
    
    /**
     * Get user activity history with pagination
     */
    public function myHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'action' => 'string|max:100',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
        ]);
        
        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }
        
        $user = Auth::user();
        $perPage = $request->input('per_page', 15);
        
        $query = UserActivity::forUser($user->id)
            ->orderBy('created_at', 'desc');
        
        // Filter by action
        if ($request->has('action')) {
            $query->byAction($request->input('action'));
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->inDateRange(
                $request->input('start_date'),
                $request->input('end_date')
            );
        }
        
        $activities = $query->paginate($perPage);
        
        // Transform data
        $activities->getCollection()->transform(function ($activity) {
            return [
                'id' => $activity->id,
                'action' => $activity->action,
                'formatted_action' => $activity->formatted_action,
                'route' => $activity->route,
                'method' => $activity->method,
                'ip_address' => $activity->ip_address,
                'browser' => $activity->browser,
                'platform' => $activity->platform,
                'is_suspicious' => $activity->isSuspicious(),
                'created_at' => $activity->created_at,
                'created_at_human' => $activity->created_at->diffForHumans(),
            ];
        });
        
        return BaseResponseService::success($activities, 'Activity history retrieved successfully');
    }
    
    /**
     * Get online users (admin only)
     */
    public function onlineUsers(Request $request)
    {
        try {
            // Authorization handled by middleware in routes
            
            $minutes = $request->input('minutes', 15); // Consider online if active in last 15 minutes
            
            // Get users with recent activity from cache
            $onlineUserIds = [];
            $cacheKeys = Cache::getRedis()->keys('user_last_activity_*');
            
            foreach ($cacheKeys as $key) {
                $userId = str_replace('user_last_activity_', '', $key);
                $lastActivity = Cache::get($key);
                
                if ($lastActivity && $lastActivity->diffInMinutes(now()) <= $minutes) {
                    $onlineUserIds[] = $userId;
                }
            }
            
            // Get user details
            $onlineUsers = User::whereIn('id', $onlineUserIds)
                ->select('id', 'name', 'email', 'last_login', 'last_activity')
                ->get()
                ->map(function ($user) {
                    $lastActivityCache = Cache::get("user_last_activity_{$user->id}");
                    
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'last_login' => $user->last_login,
                        'last_activity' => $lastActivityCache,
                        'last_activity_human' => $lastActivityCache?->diffForHumans(),
                    ];
                });
            
            return BaseResponseService::success([
                'online_users' => $onlineUsers,
                'count' => $onlineUsers->count(),
                'threshold_minutes' => $minutes
            ], 'Online users retrieved successfully');
        } catch (Exception $e) {
             Log::error('Error getting online users: ' . $e->getMessage());
             return BaseResponseService::error('Failed to get online users');
         }
    }
    
    /**
     * Get user activity statistics (admin only)
     */
    public function statistics(Request $request)
    {
        try {
            // Authorization handled by middleware in routes
            
            $days = $request->input('days', 30);
            $startDate = now()->subDays($days);
            
            // Total users
            $totalUsers = User::count();
            
            // Active users (users with login in the period)
            $activeUsers = User::where('last_login', '>=', $startDate)->count();
            
            // Users with activity (users with any activity in the period)
            $usersWithActivity = UserActivity::where('created_at', '>=', $startDate)
                ->distinct('user_id')
                ->count('user_id');
            
            // Never logged in users
            $neverLoggedIn = User::whereNull('last_login')->count();
            
            // Inactive users (no login in the period)
            $inactiveUsers = User::where(function ($query) use ($startDate) {
                $query->where('last_login', '<', $startDate)
                      ->orWhereNull('last_login');
            })->count();
            
            // Activity breakdown by action
            $activityBreakdown = UserActivity::where('created_at', '>=', $startDate)
                ->selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();
            
            // Daily activity trend
            $dailyActivity = UserActivity::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            // Browser statistics - simplified to avoid undefined properties
            $browserStats = UserActivity::where('created_at', '>=', $startDate)
                ->whereNotNull('user_agent')
                ->selectRaw('user_agent, COUNT(*) as count')
                ->groupBy('user_agent')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();
            
            // Platform statistics - simplified to avoid undefined properties
            $platformStats = UserActivity::where('created_at', '>=', $startDate)
                ->whereNotNull('user_agent')
                ->selectRaw('user_agent, COUNT(*) as count')
                ->groupBy('user_agent')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();
            
            // Suspicious activities count - simplified
            $suspiciousActivities = UserActivity::where('created_at', '>=', $startDate)
                ->where('ip_address', 'like', '%suspicious%')
                ->count();
            
            return BaseResponseService::success([
                'period_days' => $days,
                'user_stats' => [
                    'total_users' => $totalUsers,
                    'active_users' => $activeUsers,
                    'users_with_activity' => $usersWithActivity,
                    'never_logged_in' => $neverLoggedIn,
                    'inactive_users' => $inactiveUsers,
                    'activity_rate' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0,
                ],
                'activity_breakdown' => $activityBreakdown,
                'daily_activity' => $dailyActivity,
                'browser_stats' => $browserStats,
                'platform_stats' => $platformStats,
                'suspicious_activities' => $suspiciousActivities,
            ], 'Activity statistics retrieved successfully');
        } catch (Exception $e) {
             Log::error('Error getting user statistics: ' . $e->getMessage());
             return BaseResponseService::error('Failed to get user statistics');
         }
    }
    
    /**
     * Get inactive users (admin only)
     */
    public function inactiveUsers(Request $request)
    {
        try {
            // Authorization handled by middleware in routes
            
            $validator = Validator::make($request->all(), [
                'days' => 'integer|min:1|max:365',
                'per_page' => 'integer|min:1|max:100',
            ]);
            
            if ($validator->fails()) {
                return BaseResponseService::validationError($validator->errors());
            }
            
            $days = $request->input('days', 30);
            $perPage = $request->input('per_page', 15);
            $cutoffDate = now()->subDays($days);
            
            $inactiveUsers = User::select('id', 'name', 'email', 'last_login', 'last_activity', 'created_at')
                ->where(function ($query) use ($cutoffDate) {
                    $query->where('last_login', '<', $cutoffDate)
                          ->orWhereNull('last_login');
                })
                ->orderBy('last_login', 'asc')
                ->paginate($perPage);
            
            // Transform data
            $inactiveUsers->getCollection()->transform(function ($user) use ($days) {
                $daysSinceLogin = $user->last_login 
                    ? $user->last_login->diffInDays(now())
                    : $user->created_at->diffInDays(now());
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_login' => $user->last_login,
                    'last_login_human' => $user->last_login?->diffForHumans() ?? 'Never',
                    'last_activity' => $user->last_activity,
                    'last_activity_human' => $user->last_activity?->diffForHumans(),
                    'days_since_login' => $daysSinceLogin,
                    'created_at' => $user->created_at,
                    'is_new_user' => !$user->last_login && $user->created_at->diffInDays(now()) <= 7,
                ];
            });
            
            return BaseResponseService::success([
                'inactive_users' => $inactiveUsers,
                'threshold_days' => $days,
            ], 'Inactive users retrieved successfully');
        } catch (Exception $e) {
             Log::error('Error getting inactive users: ' . $e->getMessage());
             return BaseResponseService::error('Failed to get inactive users');
         }
    }
}