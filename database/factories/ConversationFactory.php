<?php

namespace Database\Factories;

use App\Domains\Conversation\Enums\ConversationStatus;
use App\Domains\Conversation\Enums\ConversationTopic;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Conversation> */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'topic' => fake()->randomElement(ConversationTopic::cases()),
            'status' => ConversationStatus::Active,
        ];
    }
}
