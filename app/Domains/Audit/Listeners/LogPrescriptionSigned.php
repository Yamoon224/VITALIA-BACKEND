<?php

namespace App\Domains\Audit\Listeners;

use App\Domains\Audit\Contracts\AuditLoggerContract;
use App\Domains\Prescription\Events\PrescriptionSigned;
use App\Models\Prescription;

class LogPrescriptionSigned
{
    public function __construct(private readonly AuditLoggerContract $auditLogger) {}

    public function handle(PrescriptionSigned $event): void
    {
        $this->auditLogger->record(
            action: 'prescription.signed',
            actorId: $event->prescription->practitioner_id,
            auditableType: Prescription::class,
            auditableId: $event->prescription->id,
            context: ['patient_id' => $event->prescription->user_id],
        );
    }
}
