<?php

namespace App\Domains\Audit\Listeners;

use App\Domains\Audit\Contracts\AuditLoggerContract;
use App\Domains\Consultation\Events\ConsultationCompleted;
use App\Models\ConsultationSession;

class LogConsultationCompleted
{
    public function __construct(private readonly AuditLoggerContract $auditLogger) {}

    public function handle(ConsultationCompleted $event): void
    {
        $this->auditLogger->record(
            action: 'consultation.completed',
            actorId: $event->session->user_id,
            auditableType: ConsultationSession::class,
            auditableId: $event->session->id,
            context: ['orientation_level' => $event->session->orientation_level?->value],
        );
    }
}
