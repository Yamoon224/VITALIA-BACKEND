<?php

namespace App\Domains\Practitioner\Exceptions;

use App\Domains\Practitioner\Enums\AppointmentStatus;
use App\Domains\Shared\Exceptions\DomainException;

class InvalidAppointmentTransitionException extends DomainException
{
    public static function from(AppointmentStatus $current, AppointmentStatus $attempted): self
    {
        return new self(
            message: "Transition impossible : {$current->value} → {$attempted->value}.",
            errorCode: 'invalid_appointment_transition',
            status: 409,
            context: ['current' => $current->value, 'attempted' => $attempted->value],
        );
    }
}
