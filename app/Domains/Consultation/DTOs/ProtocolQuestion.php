<?php

namespace App\Domains\Consultation\DTOs;

final readonly class ProtocolQuestion
{
    public function __construct(
        public string $code,
        public string $text,
    ) {}
}
