<?php

namespace App\Domains\Prescription\Enums;

/**
 * Cycle de vie d'une ordonnance (section 3, 5.7, 10.2 — pattern State).
 *
 * Transitions autorisées explicites, appliquées par
 * `PrescriptionReviewService` : `draft → in_review → signed|refused`, puis
 * `signed → transmitted`. Aucun autre chemin n'existe (section 5.7,
 * « impossibilité technique de contourner »).
 */
enum PrescriptionStatus: string
{
    case Draft = 'draft';
    case InReview = 'in_review';
    case Signed = 'signed';
    case Refused = 'refused';
    case Transmitted = 'transmitted';

    /** @return list<self> */
    public function allowedNextStates(): array
    {
        return match ($this) {
            self::Draft => [self::InReview],
            self::InReview => [self::Signed, self::Refused],
            self::Signed => [self::Transmitted],
            self::Refused, self::Transmitted => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNextStates(), true);
    }
}
