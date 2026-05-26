<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * Convenience static method: log an event.
     *
     * @param  string       $event     e.g. 'license.created', 'license.revoked'
     * @param  Model|null   $subject   The Eloquent model being acted upon
     * @param  array        $newValues New/changed values
     * @param  array        $oldValues Previous values (for updates)
     * @param  array        $meta      Extra metadata (source, reason, etc.)
     */
    public static function log(
        string  $event,
        ?Model  $subject   = null,
        array   $newValues = [],
        array   $oldValues = [],
        array   $meta      = []
    ): AuditLog {
        return AuditLog::record($event, $subject, $newValues, $oldValues, $meta);
    }

    /**
     * Log a security event (replay attack attempt, suspicious activation, etc.)
     */
    public static function security(string $reason, array $context = []): AuditLog
    {
        return AuditLog::record('security.' . $reason, null, [], [], array_merge([
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
            'path'       => request()->path(),
        ], $context));
    }
}
