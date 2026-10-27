<?php

namespace App\Domains\Billing\Contracts;

use App\Domains\Billing\Enums\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\User;

interface SubscriptionRepositoryContract
{
    public function forUser(User $user): ?Subscription;

    public function setPlan(User $user, SubscriptionPlan $plan): Subscription;
}
