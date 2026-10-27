<?php

namespace App\Domains\Conversation\Services;

use App\Domains\Audit\Contracts\AuditLoggerContract;
use App\Domains\Conversation\Enums\MessageFeedback;
use App\Models\Message;

/**
 * Retour utilisateur sur chaque réponse (section 5.2), alimentant la revue
 * qualité (section 5.13). Un retour « inquiétant » est journalisé pour que
 * le référent clinique puisse le retrouver sans attendre un échantillonnage
 * aléatoire.
 */
class MessageFeedbackService
{
    public function __construct(private readonly AuditLoggerContract $auditLogger) {}

    public function rate(Message $message, MessageFeedback $feedback): Message
    {
        $message->feedback = $feedback;
        $message->save();

        if ($feedback === MessageFeedback::Concerning) {
            $this->auditLogger->record(
                action: 'message.feedback_concerning',
                actorId: $message->conversation->user_id,
                auditableType: Message::class,
                auditableId: $message->id,
                context: ['conversation_id' => $message->conversation_id],
            );
        }

        return $message;
    }
}
