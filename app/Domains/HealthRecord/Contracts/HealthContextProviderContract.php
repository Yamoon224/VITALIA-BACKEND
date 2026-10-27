<?php

namespace App\Domains\HealthRecord\Contracts;

use App\Models\User;

/**
 * Lecture étroite exposée au domaine `Assistant` (section 10.1 — I,
 * ségrégation des interfaces) : il lit un résumé textuel pour personnaliser
 * le prompt système, sans rien connaître du dossier santé ni de la mémoire
 * de l'agent (section 6, « Gestion du contexte »).
 */
interface HealthContextProviderContract
{
    public function summaryFor(User $user): ?string;
}
