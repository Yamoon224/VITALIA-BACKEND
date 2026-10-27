<?php

namespace App\Domains\Assistant\DTOs;

/**
 * Réponse d'un fournisseur de LLM, normalisée derrière le port
 * `LlmGatewayContract` (section 6, 10.2 — Port/Adaptateur).
 *
 * `raw` conserve la charge utile brute du fournisseur pour le journal
 * d'audit (section 7, « Auditabilité ») sans que le reste de l'application
 * ait besoin de la connaître.
 */
final readonly class LlmReply
{
    /** @param  array<string, mixed>  $raw */
    public function __construct(
        public string $content,
        public string $model,
        public int $inputTokens,
        public int $outputTokens,
        public array $raw = [],
    ) {}
}
