<?php

namespace App\Domains\Assistant\DTOs;

use App\Domains\Safety\DTOs\EmergencyAssessment;

/**
 * Réponse unifiée de l'agent (section 5.4, « le niveau d'urgence détecté
 * prime sur toute autre logique »).
 *
 * Un seul type de retour pour `AssistantService::respond()`, que la réponse
 * vienne du LLM ou du court-circuit d'urgence : les appelants (contrôleur du
 * domaine `Conversation`) n'ont pas à distinguer deux chemins différents.
 */
final readonly class AssistantAnswer
{
    public function __construct(
        public string $content,
        public bool $isEmergency,
        public ?EmergencyAssessment $emergencyAssessment = null,
        public ?LlmReply $llmReply = null,
        public bool $quotaExceeded = false,
    ) {}

    public static function emergency(EmergencyAssessment $assessment): self
    {
        return new self(
            content: 'Les signaux que tu décris nécessitent une attention immédiate. '
                .'Contacte les secours de ton pays dès maintenant.',
            isEmergency: true,
            emergencyAssessment: $assessment,
        );
    }

    /**
     * Les fonctions de sécurité ne sont jamais soumises à un quota
     * (section 5.12) : ce cas n'est atteint qu'après que `SafetyPipeline` a
     * écarté toute urgence.
     */
    public static function quotaExceeded(): self
    {
        return new self(
            content: 'Tu as atteint ton quota de messages gratuits pour aujourd\'hui. '
                .'Passe à l\'offre premium pour continuer, ou reviens demain.',
            isEmergency: false,
            quotaExceeded: true,
        );
    }

    public static function fromReply(LlmReply $reply): self
    {
        return new self(content: $reply->content, isEmergency: false, llmReply: $reply);
    }
}
