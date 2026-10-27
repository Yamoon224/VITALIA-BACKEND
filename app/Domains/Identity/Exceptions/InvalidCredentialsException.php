<?php

namespace App\Domains\Identity\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

class InvalidCredentialsException extends DomainException
{
    public static function make(): self
    {
        return new self(
            message: 'Identifiants invalides.',
            errorCode: 'invalid_credentials',
            status: 401,
        );
    }
}
