<?php

namespace App\Domains\Identity\Enums;

/**
 * Consentements explicites, granulaires et révocables (section 5.1).
 */
enum ConsentType: string
{
    case HealthDataProcessing = 'health_data_processing';
    case AgentMemory = 'agent_memory';
    case PractitionerSharing = 'practitioner_sharing';
    case AnonymizedResearchUse = 'anonymized_research_use';
    case HealthConnectIntegration = 'health_connect_integration';

    /** Désactivé par défaut : l'utilisateur doit l'activer explicitement (section 5.1). */
    public function isOptInByDefault(): bool
    {
        return $this !== self::AnonymizedResearchUse;
    }
}
