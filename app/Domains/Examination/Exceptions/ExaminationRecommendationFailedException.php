<?php

namespace App\Domains\Examination\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

class ExaminationRecommendationFailedException extends DomainException
{
    public static function make(): self
    {
        return new self(
            message: 'Impossible de générer une recommandation d\'examens pour le moment.',
            errorCode: 'examination_recommendation_failed',
            status: 502,
        );
    }
}
