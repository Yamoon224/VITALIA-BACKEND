<?php

namespace App\Domains\Assistant\Contracts;

use App\Domains\Assistant\DTOs\LlmMessage;
use App\Domains\Assistant\DTOs\LlmReply;

/**
 * Port du fournisseur d'IA (section 6, 9, 10.2).
 *
 * Le reste de l'application ne connaît aucun détail du fournisseur derrière
 * ce contrat : changer de modèle, tester avec un faux fournisseur
 * (`ArrayLlmProvider`) ou basculer en cas de panne ne touche que
 * `DomainServiceProvider` (substitution de Liskov, section 10.1).
 */
interface LlmGatewayContract
{
    /**
     * @param  list<LlmMessage>  $messages
     */
    public function complete(string $systemPrompt, array $messages): LlmReply;

    /**
     * Diffuse la réponse en continu (section 6, « Streaming »).
     *
     * @param  list<LlmMessage>  $messages
     * @param  callable(string $deltaText): void  $onDelta  Appelé pour chaque fragment reçu.
     */
    public function stream(string $systemPrompt, array $messages, callable $onDelta): LlmReply;
}
