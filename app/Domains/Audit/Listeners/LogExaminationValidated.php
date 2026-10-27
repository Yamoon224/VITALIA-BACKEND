<?php

namespace App\Domains\Audit\Listeners;

use App\Domains\Audit\Contracts\AuditLoggerContract;
use App\Domains\Examination\Events\ExaminationValidated;
use App\Models\ExaminationRequest;

class LogExaminationValidated
{
    public function __construct(private readonly AuditLoggerContract $auditLogger) {}

    public function handle(ExaminationValidated $event): void
    {
        $this->auditLogger->record(
            action: 'examination.validated',
            actorId: $event->request->practitioner_id,
            auditableType: ExaminationRequest::class,
            auditableId: $event->request->id,
            context: ['patient_id' => $event->request->user_id],
        );
    }
}
