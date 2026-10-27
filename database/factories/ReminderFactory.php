<?php

namespace Database\Factories;

use App\Domains\Notification\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Reminder> */
class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => ReminderType::Medication,
            'message' => fake()->sentence(),
            'scheduled_at' => now()->addHours(8),
        ];
    }
}
