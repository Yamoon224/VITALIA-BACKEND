<?php

namespace App\Domains\Conversation\Repositories;

use App\Domains\Conversation\Contracts\ConversationRepositoryContract;
use App\Domains\Conversation\Enums\ConversationStatus;
use App\Domains\Conversation\Enums\ConversationTopic;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;

class EloquentConversationRepository implements ConversationRepositoryContract
{
    public function startFor(User $user, ConversationTopic $topic): Conversation
    {
        return Conversation::query()->create([
            'user_id' => $user->id,
            'topic' => $topic,
            'status' => ConversationStatus::Active,
        ]);
    }

    public function findForUser(int $conversationId, User $user): ?Conversation
    {
        return Conversation::query()
            ->where('user_id', $user->id)
            ->find($conversationId);
    }

    /** @return Collection<int, Conversation> */
    public function listForUser(User $user): Collection
    {
        return Conversation::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();
    }

    public function appendMessage(Conversation $conversation, Message $message): Message
    {
        $conversation->messages()->save($message);

        return $message;
    }
}
