<?php

namespace Database\Factories;

use App\Domains\Billing\Enums\SubscriptionPlan;
use App\Domains\Billing\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Subscription> */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan' => SubscriptionPlan::Free,
            'status' => SubscriptionStatus::Active,
        ];
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => SubscriptionPlan::Premium,
            'renews_at' => now()->addMonth(),
        ]);
    }
}
