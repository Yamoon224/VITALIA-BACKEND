<?php

namespace Database\Factories;

use App\Models\HealthProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<HealthProfile> */
class HealthProfileFactory extends Factory
{
    protected $model = HealthProfile::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'birth_year' => fake()->numberBetween(1950, 2015),
            'sex' => fake()->randomElement(['female', 'male', 'other']),
            'height_cm' => fake()->numberBetween(140, 200),
            'weight_kg' => fake()->numberBetween(40, 120),
            'allergies' => [],
            'chronic_conditions' => [],
            'current_medications' => [],
            'is_pregnant' => false,
        ];
    }
}
