<?php

namespace App\Domains\Prescription\Exceptions;

use App\Domains\Prescription\Enums\PrescriptionStatus;
use App\Domains\Shared\Exceptions\DomainException;

class InvalidPrescriptionTransitionException extends DomainException
{
    public static function from(PrescriptionStatus $current, PrescriptionStatus $attempted): self
    {
        return new self(
            message: "Transition impossible : {$current->value} → {$attempted->value}.",
            errorCode: 'invalid_prescription_transition',
            status: 409,
            context: ['current' => $current->value, 'attempted' => $attempted->value],
        );
    }
}
