<?php

namespace App\Domains\Billing\Contracts;

use App\Models\User;

/**
 * « Puis-je ? » : lecture étroite exposée au domaine `Assistant`
 * (section 10.1 — I, ségrégation des interfaces) pour qu'il demande sans
 * connaître les plans ni la facturation (section 5.12).
 *
 * Les fonctions de sécurité ne sont jamais soumises à un quota : ce contrat
 * n'est consulté par `AssistantService` qu'après que `SafetyPipeline` a
 * écarté toute urgence (section 5.4, « le niveau d'urgence détecté prime
 * sur toute autre logique de conversation, de monétisation ou de quota »).
 */
interface QuotaGuardContract
{
    public function canSendMessage(User $user): bool;
}
