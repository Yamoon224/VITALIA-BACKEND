<?php

namespace App\Domains\Billing\Contracts;

use App\Domains\Assistant\DTOs\LlmReply;
use App\Models\User;

/**
 * Lecture/écriture étroite exposée au domaine `Assistant` (section 10.1 —
 * I) : il journalise sa consommation sans connaître la structure de
 * facturation ni les rapports de coûts qui l'exploitent (section 5.12).
 */
interface AiUsageRecorderContract
{
    public function record(User $user, LlmReply $reply): void;
}
