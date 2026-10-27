<?php

namespace App\Domains\Consultation\Services;

use App\Domains\Consultation\Contracts\ConsultationRepositoryContract;
use App\Domains\Consultation\DTOs\ProtocolQuestion;
use App\Domains\Consultation\Exceptions\UnknownProtocolQuestionException;
use App\Models\Conversation;
use App\Models\ConsultationSession;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Protocoles cliniques paramétrables par motif de consultation
 * (section 5.3), appliqués de façon déterministe pour les questions-clés :
 * la liste des questions restantes ne dépend jamais du modèle, seulement de
 * `config('consultation_protocols')` et des réponses déjà enregistrées.
 */
class ConsultationProtocolService
{
    public function __construct(private readonly ConsultationRepositoryContract $sessions) {}

    public function start(User $user, Conversation $conversation, ?string $motif): ConsultationSession
    {
        $existing = $this->sessions->activeFor($conversation);

        if ($existing !== null) {
            return $existing;
        }

        $protocolCode = $motif !== null ? $this->detectProtocol($motif) : null;

        return $this->sessions->startFor($user, $conversation, $motif, $protocolCode);
    }

    public function nextQuestion(ConsultationSession $session): ?ProtocolQuestion
    {
        $protocol = $this->protocolFor($session);

        if ($protocol === null) {
            return null;
        }

        $answered = array_keys($session->answers ?? []);

        foreach ($protocol['questions'] as $question) {
            if (! in_array($question['code'], $answered, true)) {
                return new ProtocolQuestion($question['code'], $question['text']);
            }
        }

        return null;
    }

    public function recordAnswer(ConsultationSession $session, string $questionCode, string $answer): ConsultationSession
    {
        $protocol = $this->protocolFor($session);
        $validCodes = $protocol !== null ? array_column($protocol['questions'], 'code') : [];

        if (! in_array($questionCode, $validCodes, true)) {
            throw UnknownProtocolQuestionException::make($questionCode);
        }

        $session->answers = [...($session->answers ?? []), $questionCode => $answer];
        $session->save();

        return $session;
    }

    /** Intégré à la transcription envoyée au modèle pour la synthèse finale (section 5.3). */
    public function transcriptAddendum(ConsultationSession $session): ?string
    {
        $protocol = $this->protocolFor($session);

        if ($protocol === null || empty($session->answers)) {
            return null;
        }

        $lines = [];

        foreach ($session->answers as $code => $answer) {
            $question = collect($protocol['questions'])->firstWhere('code', $code);
            $text = $question['text'] ?? $code;
            $lines[] = "- {$text} → {$answer}";
        }

        return "Réponses au protocole « {$protocol['label']} » :\n".implode("\n", $lines);
    }

    private function detectProtocol(string $motif): ?string
    {
        $normalized = Str::lower(Str::ascii($motif));

        foreach (config('consultation_protocols', []) as $code => $protocol) {
            foreach ($protocol['keywords'] as $keyword) {
                if (str_contains($normalized, Str::ascii($keyword))) {
                    return $code;
                }
            }
        }

        return null;
    }

    /** @return array{label: string, keywords: list<string>, questions: list<array{code: string, text: string}>}|null */
    private function protocolFor(ConsultationSession $session): ?array
    {
        if ($session->protocol_code === null) {
            return null;
        }

        return config("consultation_protocols.{$session->protocol_code}");
    }
}
