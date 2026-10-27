<?php

namespace App\Domains\HealthRecord\Enums;

/**
 * Pas d'écriture silencieuse de données médicales (section 5.8) : une
 * extraction de l'agent reste `Proposed` jusqu'à validation explicite de
 * l'utilisateur.
 */
enum MemoryEntryStatus: string
{
    case Proposed = 'proposed';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}
