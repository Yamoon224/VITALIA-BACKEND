<?php

namespace Database\Factories;

use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MoodEntry> */
class MoodEntryFactory extends Factory
{
    protected $model = MoodEntry::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'mood_level' => fake()->numberBetween(1, 5),
            'recorded_at' => now(),
        ];
    }
}
