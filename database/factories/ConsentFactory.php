<?php

namespace Database\Factories;

use App\Domains\Identity\Enums\ConsentType;
use App\Models\Consent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Consent> */
class ConsentFactory extends Factory
{
    protected $model = Consent::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $granted = fake()->boolean(80);

        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(ConsentType::cases()),
            'granted' => $granted,
            'granted_at' => $granted ? now() : null,
            'revoked_at' => $granted ? null : now(),
        ];
    }
}
