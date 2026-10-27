<?php

namespace Database\Factories;

use App\Domains\Consultation\Enums\ConsultationStatus;
use App\Models\ConsultationSession;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ConsultationSession> */
class ConsultationSessionFactory extends Factory
{
    protected $model = ConsultationSession::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'user_id' => User::factory(),
            'status' => ConsultationStatus::InProgress,
        ];
    }
}
