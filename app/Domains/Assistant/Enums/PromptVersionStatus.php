<?php

namespace App\Domains\Assistant\Enums;

/**
 * Cycle de vie d'une version de bloc de prompt (section 5.13) :
 * brouillon → publiée. Un retour arrière republie une ancienne version sous
 * un nouveau numéro plutôt que de réécrire l'historique (section 9,
 * traçabilité).
 */
enum PromptVersionStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
