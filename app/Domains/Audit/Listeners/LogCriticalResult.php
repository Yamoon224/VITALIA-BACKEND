<?php

namespace App\Domains\Audit\Listeners;

use App\Domains\Audit\Contracts\AuditLoggerContract;
use App\Domains\Examination\Events\CriticalResultRecorded;
use App\Models\ExaminationResult;

class LogCriticalResult
{
    public function __construct(private readonly AuditLoggerContract $auditLogger) {}

    public function handle(CriticalResultRecorded $event): void
    {
        $this->auditLogger->record(
            action: 'examination_result.critical',
            actorId: $event->result->user_id,
            auditableType: ExaminationResult::class,
            auditableId: $event->result->id,
            context: ['parameter' => $event->result->parameter],
        );
    }
}
