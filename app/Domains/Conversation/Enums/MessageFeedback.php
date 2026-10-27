<?php

namespace App\Domains\Conversation\Enums;

/**
 * Retour utilisateur sur chaque réponse (section 5.2), alimentant la revue
 * qualité du référent clinique (section 5.13). `Concerning` est un signal de
 * qualité pour la revue, distinct des urgences réelles qui passent par le
 * domaine `Safety` indépendamment de tout retour utilisateur.
 */
enum MessageFeedback: string
{
    case Useful = 'useful';
    case NotUseful = 'not_useful';
    case Concerning = 'concerning';
}
