<?php

namespace Database\Factories;

use App\Models\PractitionerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PractitionerProfile> */
class PractitionerProfileFactory extends Factory
{
    protected $model = PractitionerProfile::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'specialty' => fake()->randomElement(['Médecine générale', 'Pédiatrie', 'Gynécologie', 'Dermatologie']),
            'license_number' => fake()->unique()->numerify('LIC-#####'),
            'verified_at' => now(),
            'languages' => ['fr'],
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['verified_at' => null]);
    }
}
