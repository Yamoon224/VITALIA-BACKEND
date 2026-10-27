<?php

namespace App\Domains\Safety\Enums;

/**
 * Niveaux d'orientation (section 5.3 et 5.4).
 *
 * `Emergency` prime sur toute autre logique de conversation, de
 * monétisation ou de quota (section 5.4, dernier point) : tout code qui lit
 * ce niveau doit court-circuiter le reste du flux avant toute autre
 * décision.
 */
enum UrgencyLevel: string
{
    case Emergency = 'emergency';
    case UrgentConsultation = 'urgent_consultation_24h';
    case ScheduledConsultation = 'scheduled_consultation';
    case SelfCareWithMonitoring = 'self_care_with_monitoring';

    public function requiresImmediateEscalation(): bool
    {
        return $this === self::Emergency;
    }
}
