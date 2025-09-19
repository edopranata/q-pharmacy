<?php

namespace App\Traits;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    /**
     * Boot the auditable trait
     */
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLogger::logCreated($model);
        });

        static::updated(function ($model) {
            // Only log if there are actual changes
            if ($model->wasChanged() && !empty($model->getChanges())) {
                AuditLogger::logUpdated($model);
            }
        });

        static::deleted(function ($model) {
            AuditLogger::logDeleted($model);
        });
    }

    /**
     * Get all audit logs for this model
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(
            \App\Models\AuditLog::class,
            'auditable',
            'model_type',
            'model_id'
        );
    }

    /**
     * Get recent audit logs for this model
     */
    public function getRecentAuditLogs(int $limit = 10)
    {
        return AuditLogger::getModelAuditLogs($this, $limit);
    }

    /**
     * Log a custom audit event for this model
     */
    public function logAudit(string $action, ?array $data = null, ?string $description = null)
    {
        return AuditLogger::log($action, $this, null, $data, $description);
    }

    /**
     * Check if model should be audited
     * Override this method in models to add conditions
     */
    protected function shouldAudit(): bool
    {
        return true;
    }

    /**
     * Get attributes that should be excluded from audit
     * Override this method in models to exclude sensitive data
     */
    protected function getAuditExclude(): array
    {
        return [
            'password',
            'remember_token',
            'email_verified_at',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Get the model data for auditing (excluding sensitive fields)
     */
    public function getAuditableData(): array
    {
        $data = $this->toArray();
        $exclude = $this->getAuditExclude();
        
        return array_diff_key($data, array_flip($exclude));
    }
}