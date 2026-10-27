<?php

namespace App\Domains\Assistant\Services;

use App\Domains\Assistant\Contracts\LlmGatewayContract;
use App\Domains\Assistant\DTOs\AssistantAnswer;
use App\Domains\Assistant\DTOs\LlmMessage;
use App\Domains\Assistant\DTOs\LlmReply;
use App\Domains\Billing\Contracts\AiUsageRecorderContract;
use App\Domains\Billing\Contracts\QuotaGuardContract;
use App\Domains\Safety\Services\SafetyPipeline;
use App\Models\Conversation;
use App\Models\User;

/**
 * Orchestration d'un tour de conversation (section 6, 10.2).
 *
 * Ordre impératif : la détection d'urgence (`SafetyPipeline::before()`) a
 * toujours lieu avant l'appel au modèle et court-circuite tout le reste si
 * elle détecte un signal de gravité (section 5.4, « prime sur toute autre
 * logique »). Le quota (section 5.12) n'est vérifié qu'ensuite — jamais
 * avant la détection d'urgence. Le contrôle de conformité (`after()`) a
 * toujours lieu après l'appel au modèle, avant que la réponse ne soit
 * retournée à l'appelant.
 */
class AssistantService
{
    public function __construct(
        private readonly SafetyPipeline $safety,
        private readonly LlmGatewayContract $llmGateway,
        private readonly PromptBuilder $promptBuilder,
        private readonly QuotaGuardContract $quota,
        private readonly AiUsageRecorderContract $usage,
    ) {}

    public function respond(
        User $user,
        Conversation $conversation,
        string $userMessage,
        ?string $consultationProtocol = null,
        ?string $healthProfileSummary = null,
    ): AssistantAnswer {
        $shortCircuit = $this->checkGuards($user, $conversation, $userMessage);

        if ($shortCircuit !== null) {
            return $shortCircuit;
        }

        $systemPrompt = $this->promptBuilder->build($user, $consultationProtocol, $healthProfileSummary);
        $reply = $this->llmGateway->complete($systemPrompt, [LlmMessage::user($userMessage)]);
        $this->usage->record($user, $reply);

        return $this->finalizeReply($reply);
    }

    /**
     * Variante diffusée en continu (section 6, « Streaming »).
     *
     * Les garde-fous d'avant-modèle (urgence, quota) s'appliquent de façon
     * identique à `respond()` : rien n'est diffusé au client tant qu'ils
     * n'ont pas été évalués. `$onDelta` n'est jamais appelé pour une réponse
     * court-circuitée (section 11.1 : l'écran d'urgence est produit par le
     * code, pas par du texte diffusé).
     */
    public function respondStreaming(
        User $user,
        Conversation $conversation,
        string $userMessage,
        callable $onDelta,
        ?string $consultationProtocol = null,
        ?string $healthProfileSummary = null,
    ): AssistantAnswer {
        $shortCircuit = $this->checkGuards($user, $conversation, $userMessage);

        if ($shortCircuit !== null) {
            return $shortCircuit;
        }

        $systemPrompt = $this->promptBuilder->build($user, $consultationProtocol, $healthProfileSummary);
        $reply = $this->llmGateway->stream($systemPrompt, [LlmMessage::user($userMessage)], $onDelta);
        $this->usage->record($user, $reply);

        return $this->finalizeReply($reply);
    }

    /** @return AssistantAnswer|null Une réponse à retourner immédiatement, ou `null` pour poursuivre vers le modèle. */
    private function checkGuards(User $user, Conversation $conversation, string $userMessage): ?AssistantAnswer
    {
        $assessment = $this->safety->before($user, $conversation, $userMessage);

        if ($assessment->isEmergency()) {
            return AssistantAnswer::emergency($assessment);
        }

        if (! $this->quota->canSendMessage($user)) {
            return AssistantAnswer::quotaExceeded();
        }

        return null;
    }

    private function finalizeReply(LlmReply $reply): AssistantAnswer
    {
        $compliance = $this->safety->after($reply->content);

        if (! $compliance->compliant) {
            return new AssistantAnswer(
                content: 'Je ne peux pas formuler cette réponse ainsi. '
                    .'Je t\'invite à consulter un professionnel de santé pour en discuter.',
                isEmergency: false,
                llmReply: $reply,
            );
        }

        return AssistantAnswer::fromReply($reply);
    }
}
