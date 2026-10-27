<?php

namespace App\Domains\Examination\Enums;

/**
 * Émission d'une prescription d'examens officielle uniquement après
 * signature (section 5.6) — même garde que `PrescriptionStatus`.
 */
enum ExaminationRequestStatus: string
{
    case Draft = 'draft';
    case Validated = 'validated';
    case Refused = 'refused';

    /** @return list<self> */
    public function allowedNextStates(): array
    {
        return match ($this) {
            self::Draft => [self::Validated, self::Refused],
            self::Validated, self::Refused => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNextStates(), true);
    }
}
