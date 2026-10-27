<?php

namespace App\Domains\Assistant\Providers;

use App\Domains\Assistant\Contracts\LlmGatewayContract;
use App\Domains\Assistant\DTOs\LlmMessage;
use App\Domains\Assistant\DTOs\LlmReply;

/**
 * Faux fournisseur déterministe pour les tests (section 12.1, « Les tests de
 * l'IA n'appellent pas le vrai fournisseur dans la CI standard »).
 *
 * Enregistré comme singleton sous son propre nom par `DomainServiceProvider`
 * (pas seulement derrière le contrat) : les tests le redemandent par sa
 * classe concrète pour préparer une réponse (`queue()`) ou inspecter les
 * appels reçus (`sentMessages()`), et doivent obtenir l'instance exacte que
 * les services ont reçue.
 */
class ArrayLlmProvider implements LlmGatewayContract
{
    /** @var list<LlmReply> */
    private array $queue = [];

    /** @var list<array{system: string, messages: list<LlmMessage>}> */
    private array $calls = [];

    public function queue(LlmReply $reply): void
    {
        $this->queue[] = $reply;
    }

    public function complete(string $systemPrompt, array $messages): LlmReply
    {
        $this->calls[] = ['system' => $systemPrompt, 'messages' => $messages];

        return array_shift($this->queue) ?? new LlmReply(
            content: 'Réponse de test.',
            model: 'array-fake',
            inputTokens: 0,
            outputTokens: 0,
        );
    }

    public function stream(string $systemPrompt, array $messages, callable $onDelta): LlmReply
    {
        $reply = $this->complete($systemPrompt, $messages);
        $onDelta($reply->content);

        return $reply;
    }

    /** @return list<array{system: string, messages: list<LlmMessage>}> */
    public function sentMessages(): array
    {
        return $this->calls;
    }
}
