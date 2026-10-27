<?php

namespace App\Domains\Examination\Services;

use App\Domains\Assistant\Contracts\LlmGatewayContract;
use App\Domains\Assistant\DTOs\LlmMessage;
use App\Domains\Examination\Contracts\ExaminationRequestRepositoryContract;
use App\Domains\Examination\Exceptions\ExaminationRecommendationFailedException;
use App\Domains\Examination\Support\ExaminationRecommendationValidator;
use App\Models\Conversation;
use App\Models\ExaminationRequest;
use App\Models\User;

/**
 * Recommandation d'examens justifiée par la situation clinique
 * (section 5.6) : le modèle propose, un praticien valide
 * (`ExaminationReviewService`) — jamais d'émission directe.
 */
class ExaminationRecommendationService
{
    private const string SYSTEM_PROMPT = <<<'PROMPT'
        Tu es le module de recommandation d'examens de VITALIA. À partir de la
        transcription de consultation fournie, produis EXCLUSIVEMENT un
        tableau JSON valide, sans texte autour, de la forme :
        [{"name": "nom de l'examen", "reason": "justification clinique", "prep_instructions": "préparation nécessaire ou chaîne vide"}]
        Ne recommande que des examens courants et sûrs (biologie, imagerie
        simple). Si aucun examen n'est pertinent, renvoie un tableau vide.
        PROMPT;

    public function __construct(
        private readonly ExaminationRequestRepositoryContract $requests,
        private readonly LlmGatewayContract $llmGateway,
        private readonly ExaminationRecommendationValidator $validator,
    ) {}

    public function generate(User $user, Conversation $conversation): ExaminationRequest
    {
        $transcript = $conversation->messages
            ->map(fn ($message) => "{$message->sender_type->value}: {$message->content}")
            ->implode("\n");

        $items = $this->attempt($transcript) ?? $this->attempt($transcript);

        if ($items === null) {
            throw ExaminationRecommendationFailedException::make();
        }

        return $this->requests->createDraft($user, $items, $conversation->id);
    }

    /** @return list<array{name: string, reason: string, prep_instructions: string}>|null */
    private function attempt(string $transcript): ?array
    {
        $reply = $this->llmGateway->complete(self::SYSTEM_PROMPT, [LlmMessage::user($transcript)]);

        return $this->validator->validate($reply->content);
    }
}
