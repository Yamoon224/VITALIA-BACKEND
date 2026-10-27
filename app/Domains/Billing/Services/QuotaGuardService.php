<?php

namespace App\Domains\Billing\Services;

use App\Domains\Billing\Contracts\QuotaGuardContract;
use App\Domains\Billing\Contracts\SubscriptionRepositoryContract;
use App\Domains\Conversation\Enums\MessageSenderType;
use App\Models\Message;
use App\Models\User;

class QuotaGuardService implements QuotaGuardContract
{
    public function __construct(private readonly SubscriptionRepositoryContract $subscriptions) {}

    public function canSendMessage(User $user): bool
    {
        $subscription = $this->subscriptions->forUser($user);

        if ($subscription?->isActivePremium()) {
            return true;
        }

        $limit = (int) config('assistant.quotas.free_messages_per_day');

        return $this->messagesSentToday($user) < $limit;
    }

    private function messagesSentToday(User $user): int
    {
        return Message::query()
            ->whereHas('conversation', fn ($query) => $query->where('user_id', $user->id))
            ->where('sender_type', MessageSenderType::User)
            ->whereDate('created_at', today())
            ->count();
    }
}
