<?php

namespace App\Domains\Consultation\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

/**
 * Levée après un deuxième échec de génération conforme au schéma
 * (section 6) : mieux vaut échouer explicitement que persister une
 * synthèse non fiable.
 */
class SynthesisGenerationFailedException extends DomainException
{
    public static function make(): self
    {
        return new self(
            message: 'La synthèse clinique n\'a pas pu être générée. Réessaie ou contacte un praticien.',
            errorCode: 'synthesis_generation_failed',
            status: 502,
        );
    }
}
