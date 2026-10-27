<?php

namespace App\Domains\Assistant\DTOs;

/**
 * Message unitaire envoyé au fournisseur de LLM.
 *
 * `role` suit la convention de l'API Claude (`user` / `assistant`) : le
 * prompt système n'est pas un message, il est porté séparément par
 * `LlmGatewayContract::complete()` (section 6, « Prompt système »).
 */
final readonly class LlmMessage
{
    public function __construct(
        public string $role,
        public string $content,
    ) {}

    public static function user(string $content): self
    {
        return new self('user', $content);
    }

    public static function assistant(string $content): self
    {
        return new self('assistant', $content);
    }
}
