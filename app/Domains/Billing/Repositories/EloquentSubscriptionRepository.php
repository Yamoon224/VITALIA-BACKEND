<?php

namespace App\Domains\Billing\Repositories;

use App\Domains\Billing\Contracts\SubscriptionRepositoryContract;
use App\Domains\Billing\Enums\SubscriptionPlan;
use App\Domains\Billing\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\User;

class EloquentSubscriptionRepository implements SubscriptionRepositoryContract
{
    public function forUser(User $user): ?Subscription
    {
        return Subscription::query()->where('user_id', $user->id)->first();
    }

    public function setPlan(User $user, SubscriptionPlan $plan): Subscription
    {
        return Subscription::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['plan' => $plan, 'status' => SubscriptionStatus::Active, 'renews_at' => now()->addMonth()],
        );
    }
}
