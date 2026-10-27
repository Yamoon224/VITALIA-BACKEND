<?php

namespace App\Domains\Conversation\Contracts;

use App\Domains\Conversation\Enums\ConversationTopic;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;

interface ConversationRepositoryContract
{
    public function startFor(User $user, ConversationTopic $topic): Conversation;

    public function findForUser(int $conversationId, User $user): ?Conversation;

    /** @return Collection<int, Conversation> */
    public function listForUser(User $user): Collection;

    public function appendMessage(Conversation $conversation, Message $message): Message;
}
