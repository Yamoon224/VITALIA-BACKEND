<?php

namespace App\Domains\HealthRecord\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

class MemoryEntryAlreadyReviewedException extends DomainException
{
    public static function make(): self
    {
        return new self(
            message: 'Cette proposition a déjà été traitée.',
            errorCode: 'memory_entry_already_reviewed',
            status: 409,
        );
    }
}
