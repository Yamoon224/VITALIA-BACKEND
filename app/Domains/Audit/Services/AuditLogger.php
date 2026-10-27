<?php

namespace App\Domains\Audit\Services;

use App\Domains\Audit\Contracts\AuditLoggerContract;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AuditLogger implements AuditLoggerContract
{
    public function record(
        string $action,
        ?int $actorId = null,
        ?string $auditableType = null,
        ?int $auditableId = null,
        array $context = [],
    ): void {
        AuditLog::query()->create([
            'actor_id' => $actorId,
            'action' => $action,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'context' => $context,
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
