<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model untuk tracking aktivitas pengguna
 * 
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property string $route
 * @property string $method
 * @property string $ip_address
 * @property string|null $user_agent
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class UserActivity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'route',
        'method',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk aktivitas hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope untuk aktivitas minggu ini
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope untuk aktivitas bulan ini
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    /**
     * Get formatted action name
     */
    public function getFormattedActionAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->action));
    }

    /**
     * Get browser name from user agent
     */
    public function getBrowserAttribute(): ?string
    {
        if (!$this->user_agent) {
            return null;
        }

        $userAgent = $this->user_agent;
        
        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($userAgent, 'Edge')) {
            return 'Edge';
        } elseif (str_contains($userAgent, 'Opera')) {
            return 'Opera';
        }
        
        return 'Unknown';
    }

    /**
     * Get platform from user agent
     */
    public function getPlatformAttribute(): ?string
    {
        if (!$this->user_agent) {
            return null;
        }

        $userAgent = $this->user_agent;
        
        if (str_contains($userAgent, 'Windows')) {
            return 'Windows';
        } elseif (str_contains($userAgent, 'Macintosh')) {
            return 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            return 'Linux';
        } elseif (str_contains($userAgent, 'Android')) {
            return 'Android';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            return 'iOS';
        }
        
        return 'Unknown';
    }

    /**
     * Check if activity is suspicious
     */
    public function isSuspicious(): bool
    {
        $suspiciousActions = [
            'rapid_requests',
            'ip_change',
            'user_agent_change',
            'failed_login_attempt',
            'unauthorized_access'
        ];
        
        return in_array($this->action, $suspiciousActions);
    }

    /**
     * Get activity statistics for a user
     */
    public static function getStatsForUser(int $userId, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $activities = self::forUser($userId)
            ->where('created_at', '>=', $startDate)
            ->get();
            
        return [
            'total_activities' => $activities->count(),
            'unique_actions' => $activities->pluck('action')->unique()->count(),
            'most_common_action' => $activities->groupBy('action')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),
            'unique_ips' => $activities->pluck('ip_address')->unique()->count(),
            'browsers_used' => $activities->map->browser->unique()->filter()->values(),
            'platforms_used' => $activities->map->platform->unique()->filter()->values(),
            'suspicious_activities' => $activities->filter->isSuspicious()->count(),
        ];
    }

    /**
     * Clean old activities (for maintenance)
     */
    public static function cleanOldActivities(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        return self::where('created_at', '<', $cutoffDate)->delete();
    }
}