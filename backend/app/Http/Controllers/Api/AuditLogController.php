<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditLogger;
use App\Services\BaseResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs
     */
    public function index(Request $request): JsonResponse
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->has('action')) {
            $query->action($request->action);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by model
        if ($request->has('model_type') && $request->has('model_id')) {
            $query->forModel($request->model_type, $request->model_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        $logs = $query->paginate($request->get('per_page', 15));

        return BaseResponseService::paginated($logs, 'Audit logs retrieved successfully');
    }

    /**
     * Display the specified audit log
     */
    public function show(AuditLog $auditLog): JsonResponse
    {
        $auditLog->load('user');
        
        return BaseResponseService::success($auditLog, 'Audit log retrieved successfully');
    }

    /**
     * Get recent audit logs
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 50);
        $logs = AuditLogger::getRecentLogs($limit);
        
        return BaseResponseService::success($logs, 'Recent audit logs retrieved successfully');
    }

    /**
     * Get audit logs for a specific model
     */
    public function forModel(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        $logs = AuditLogger::getLogsForModel(
            $request->model_type,
            $request->model_id
        );
        
        return BaseResponseService::success($logs, 'Model audit logs retrieved successfully');
    }

    /**
     * Clean old audit logs
     */
    public function clean(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'days_to_keep' => 'integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        $daysToKeep = $request->get('days_to_keep', 90);
        $deletedCount = AuditLogger::cleanOldLogs($daysToKeep);
        
        return BaseResponseService::success([
            'deleted_count' => $deletedCount,
        ], "Cleaned {$deletedCount} old audit logs");
    }
}
