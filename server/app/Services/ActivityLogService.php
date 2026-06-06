<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log a procurement activity to the activity_logs table.
     */
    public static function log(string $module, string $referenceType, int $referenceId, string $action, ?string $description = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => auth('api')->id(),
            'module' => $module,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'action' => $action,
            'description' => $description,
            'created_at' => now()->timestamp,
        ]);
    }
}
