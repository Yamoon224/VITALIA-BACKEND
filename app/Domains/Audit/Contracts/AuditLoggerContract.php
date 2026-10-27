<?php

namespace App\Domains\Audit\Contracts;

interface AuditLoggerContract
{
    /**
     * @param  array<string, mixed>  $context  Jamais de donnée de santé en clair (section 7).
     */
    public function record(
        string $action,
        ?int $actorId = null,
        ?string $auditableType = null,
        ?int $auditableId = null,
        array $context = [],
    ): void;
}
