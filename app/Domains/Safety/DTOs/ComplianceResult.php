<?php

namespace App\Domains\Safety\DTOs;

final readonly class ComplianceResult
{
    /** @param  list<string>  $violations */
    public function __construct(
        public bool $compliant,
        public array $violations = [],
    ) {}

    public static function pass(): self
    {
        return new self(true);
    }

    /** @param  list<string>  $violations */
    public static function fail(array $violations): self
    {
        return new self(false, $violations);
    }
}
