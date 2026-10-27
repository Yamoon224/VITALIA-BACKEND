<?php

namespace Database\Factories;

use App\Domains\Wellness\Enums\WellnessGoalType;
use App\Models\User;
use App\Models\WellnessGoal;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WellnessGoal> */
class WellnessGoalFactory extends Factory
{
    protected $model = WellnessGoal::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(WellnessGoalType::cases()),
            'target_value' => fake()->numberBetween(5, 100),
            'current_value' => 0,
        ];
    }
}
