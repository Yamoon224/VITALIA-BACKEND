<?php

namespace App\Domains\Consultation\Services;

use App\Domains\Assistant\Contracts\LlmGatewayContract;
use App\Domains\Assistant\DTOs\LlmMessage;
use App\Domains\Consultation\Contracts\ConsultationRepositoryContract;
use App\Domains\Consultation\DTOs\ConsultationSynthesis;
use App\Domains\Consultation\Enums\ConsultationStatus;
use App\Domains\Consultation\Events\ConsultationCompleted;
use App\Domains\Consultation\Exceptions\SynthesisGenerationFailedException;
use App\Domains\Consultation\Support\SynthesisValidator;
use App\Models\ConsultationSession;
use App\Models\Conversation;

/**
 * Génère la synthèse structurée en fin d'entretien (section 5.3).
 *
 * Une sortie non conforme au schéma est régénérée une fois avant d'échouer
 * (section 6, « Sorties structurées ») : jamais affichée telle quelle à
 * l'utilisateur ni persistée sans validation.
 */
class ConsultationSynthesisService
{
    private const string SYSTEM_PROMPT = <<<'PROMPT'
        Tu es le module de synthèse clinique de VITALIA. À partir de la
        transcription fournie, produis EXCLUSIVEMENT un objet JSON valide,
        sans texte autour, de la forme :
        {
          "summary": "résumé clinique en 2-3 phrases",
          "hypotheses": [{"condition": "nom de l'hypothèse", "confidence": 0.0 à 1.0}],
          "orientation_level": "emergency|urgent_consultation_24h|scheduled_consultation|self_care_with_monitoring",
          "red_flags": ["signe d'alerte à surveiller"]
        }
        Ne pose jamais de diagnostic définitif : les hypothèses restent
        probabilistes. N'invente pas de niveau d'orientation en dehors des
        quatre valeurs listées.
        PROMPT;

    public function __construct(
        private readonly ConsultationRepositoryContract $sessions,
        private readonly LlmGatewayContract $llmGateway,
        private readonly SynthesisValidator $validator,
        private readonly ConsultationProtocolService $protocol,
    ) {}

    public function finish(ConsultationSession $session, Conversation $conversation): ConsultationSession
    {
        $transcript = $this->buildTranscript($conversation, $session);

        $synthesis = $this->generate($transcript) ?? $this->generate($transcript);

        if ($synthesis === null) {
            throw SynthesisGenerationFailedException::make();
        }

        $session->status = ConsultationStatus::Completed;
        $session->summary = $synthesis->summary;
        $session->hypotheses = $synthesis->hypotheses;
        $session->orientation_level = $synthesis->orientationLevel;
        $session->red_flags = $synthesis->redFlags;
        $session->completed_at = now();

        $saved = $this->sessions->save($session);

        ConsultationCompleted::dispatch($saved);

        return $saved;
    }

    private function generate(string $transcript): ?ConsultationSynthesis
    {
        $reply = $this->llmGateway->complete(self::SYSTEM_PROMPT, [LlmMessage::user($transcript)]);

        return $this->validator->validate($reply->content);
    }

    private function buildTranscript(Conversation $conversation, ConsultationSession $session): string
    {
        $transcript = $conversation->messages
            ->map(fn ($message) => "{$message->sender_type->value}: {$message->content}")
            ->implode("\n");

        $addendum = $this->protocol->transcriptAddendum($session);

        return $addendum === null ? $transcript : "{$transcript}\n\n{$addendum}";
    }
}
