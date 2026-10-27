<?php

namespace App\Domains\Conversation\Services;

use App\Domains\Assistant\DTOs\AssistantAnswer;
use App\Domains\Assistant\Services\AssistantService;
use App\Domains\Conversation\Contracts\ConversationRepositoryContract;
use App\Domains\Conversation\Enums\ConversationTopic;
use App\Domains\Conversation\Enums\MessageSenderType;
use App\Domains\HealthRecord\Contracts\HealthContextProviderContract;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

/**
 * Façade du domaine `Conversation` (section 5.2).
 *
 * Seul point d'entrée utilisé par les contrôleurs HTTP : il orchestre la
 * persistance des messages et l'appel à l'agent, sans que le contrôleur ait
 * à connaître `AssistantService` ni la chaîne de sécurité qu'il porte.
 */
class ConversationService
{
    public function __construct(
        private readonly ConversationRepositoryContract $conversations,
        private readonly AssistantService $assistant,
        private readonly HealthContextProviderContract $healthContext,
    ) {}

    public function start(User $user, ConversationTopic $topic): Conversation
    {
        return $this->conversations->startFor($user, $topic);
    }

    public function sendMessage(User $user, Conversation $conversation, string $content): AssistantAnswer
    {
        $this->conversations->appendMessage($conversation, new Message([
            'sender_type' => MessageSenderType::User,
            'content' => $content,
        ]));

        $answer = $this->assistant->respond(
            $user,
            $conversation,
            $content,
            healthProfileSummary: $this->healthContext->summaryFor($user),
        );

        $this->conversations->appendMessage($conversation, new Message([
            'sender_type' => MessageSenderType::Assistant,
            'content' => $answer->content,
        ]));

        return $answer;
    }

    /**
     * Variante diffusée en continu (section 5.2, 6). `$onDelta` est appelé
     * pour chaque fragment de texte reçu du modèle ; le message complet est
     * tout de même persisté à la fin, comme pour `sendMessage()`.
     */
    public function sendMessageStreaming(
        User $user,
        Conversation $conversation,
        string $content,
        callable $onDelta,
    ): AssistantAnswer {
        $this->conversations->appendMessage($conversation, new Message([
            'sender_type' => MessageSenderType::User,
            'content' => $content,
        ]));

        $answer = $this->assistant->respondStreaming(
            $user,
            $conversation,
            $content,
            $onDelta,
            healthProfileSummary: $this->healthContext->summaryFor($user),
        );

        $this->conversations->appendMessage($conversation, new Message([
            'sender_type' => MessageSenderType::Assistant,
            'content' => $answer->content,
        ]));

        return $answer;
    }
}
