<?php

namespace App\Domains\Examination\Exceptions;

use App\Domains\Examination\Enums\ExaminationRequestStatus;
use App\Domains\Shared\Exceptions\DomainException;

class InvalidExaminationTransitionException extends DomainException
{
    public static function from(ExaminationRequestStatus $current, ExaminationRequestStatus $attempted): self
    {
        return new self(
            message: "Transition impossible : {$current->value} → {$attempted->value}.",
            errorCode: 'invalid_examination_transition',
            status: 409,
            context: ['current' => $current->value, 'attempted' => $attempted->value],
        );
    }
}
