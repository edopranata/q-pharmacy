<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Log an audit event
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'description' => $description ?? self::generateDescription($action, $model),
        ]);
    }

    /**
     * Log model creation
     */
    public static function logCreated(Model $model, ?string $description = null): AuditLog
    {
        return self::log(
            'created',
            $model,
            null,
            $model->toArray(),
            $description
        );
    }

    /**
     * Log model update
     */
    public static function logUpdated(Model $model, ?string $description = null): AuditLog
    {
        return self::log(
            'updated',
            $model,
            $model->getOriginal(),
            $model->getChanges(),
            $description
        );
    }

    /**
     * Log model deletion
     */
    public static function logDeleted(Model $model, ?string $description = null): AuditLog
    {
        return self::log(
            'deleted',
            $model,
            $model->toArray(),
            null,
            $description
        );
    }

    /**
     * Log authentication events
     */
    public static function logAuth(string $action, ?Model $user = null, ?string $description = null): AuditLog
    {
        return self::log(
            $action,
            $user,
            null,
            null,
            $description
        );
    }

    /**
     * Log system events
     */
    public static function logSystem(string $action, ?string $description = null): AuditLog
    {
        return self::log(
            $action,
            null,
            null,
            null,
            $description
        );
    }

    /**
     * Generate a human-readable description
     */
    private static function generateDescription(string $action, ?Model $model = null): string
    {
        if (!$model) {
            return ucfirst($action) . ' action performed';
        }

        $modelName = class_basename($model);
        $identifier = $model->name ?? $model->title ?? $model->id ?? 'record';

        return match ($action) {
            'created' => "Created {$modelName}: {$identifier}",
            'updated' => "Updated {$modelName}: {$identifier}",
            'deleted' => "Deleted {$modelName}: {$identifier}",
            'viewed' => "Viewed {$modelName}: {$identifier}",
            'exported' => "Exported {$modelName} data",
            'imported' => "Imported {$modelName} data",
            default => ucfirst($action) . " {$modelName}: {$identifier}",
        };
    }

    /**
     * Get audit logs for a specific model
     */
    public static function getModelAuditLogs(Model $model, int $limit = 50)
    {
        return AuditLog::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for a specific user
     */
    public static function getUserAuditLogs(int $userId, int $limit = 100)
    {
        return AuditLog::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent audit logs
     */
    public static function getRecentLogs(int $limit = 100)
    {
        return AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for a specific model
     */
    public static function getLogsForModel(string $modelType, int $modelId)
    {
        return AuditLog::with('user')
            ->forModel($modelType, $modelId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Clean old audit logs
     */
    public static function cleanOldLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        return AuditLog::where('created_at', '<', $cutoffDate)->delete();
    }
}