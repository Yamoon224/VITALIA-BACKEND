<?php

namespace Database\Factories;

use App\Domains\Conversation\Enums\MessageSenderType;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Message> */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_type' => fake()->randomElement(MessageSenderType::cases()),
            'content' => fake()->sentence(),
        ];
    }
}
