<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public static function log(
        string $logType,
        $subject,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null
    ): void
    {
        AuditLog::create([
            'log_type' => $logType,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'performed_by' => Auth::check() ? Auth::id() : null,
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => request()->ip(),
            'description' => $description,
        ]);
    }
}
