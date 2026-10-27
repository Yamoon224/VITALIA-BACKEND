<?php

namespace App\Domains\Audit\Listeners;

use App\Domains\Audit\Contracts\AuditLoggerContract;
use App\Domains\Safety\Events\EmergencyDetected;
use App\Models\EmergencyDetection;

/**
 * Observateur (section 10.2) : `Safety` détecte et journalise sa propre
 * ligne métier ; `Audit` ajoute l'entrée dans le journal inviolable sans que
 * `Safety` ait besoin de connaître son existence.
 */
class LogEmergencyDetection
{
    public function __construct(private readonly AuditLoggerContract $auditLogger) {}

    public function handle(EmergencyDetected $event): void
    {
        $this->auditLogger->record(
            action: 'emergency.detected',
            actorId: $event->detection->user_id,
            auditableType: EmergencyDetection::class,
            auditableId: $event->detection->id,
            context: [
                'level' => $event->detection->level->value,
                'conversation_id' => $event->detection->conversation_id,
            ],
        );
    }
}
