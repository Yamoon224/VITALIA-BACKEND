<?php

namespace App\Domains\Consultation\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

class UnknownProtocolQuestionException extends DomainException
{
    public static function make(string $questionCode): self
    {
        return new self(
            message: "Question « {$questionCode} » inconnue pour ce protocole.",
            errorCode: 'unknown_protocol_question',
            status: 422,
            context: ['question_code' => $questionCode],
        );
    }
}
