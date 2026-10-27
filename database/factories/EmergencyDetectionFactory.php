<?php

namespace Database\Factories;

use App\Domains\Safety\Enums\UrgencyLevel;
use App\Models\EmergencyDetection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EmergencyDetection> */
class EmergencyDetectionFactory extends Factory
{
    protected $model = EmergencyDetection::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'conversation_id' => null,
            'level' => UrgencyLevel::Emergency,
            'matched_signals' => ['douleur thoracique'],
            'mental_health_protocol' => null,
            'message_excerpt' => fake()->sentence(),
        ];
    }
}
