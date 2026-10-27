<?php

namespace Database\Factories;

use App\Domains\Prescription\Enums\PrescriptionStatus;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Prescription> */
class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => PrescriptionStatus::Draft,
            'items' => [[
                'dci' => 'Paracétamol',
                'dosage' => '1g',
                'duration_days' => 3,
            ]],
            'safety_alerts' => [],
        ];
    }

    public function inReview(): static
    {
        return $this->state(fn (array $attributes) => ['status' => PrescriptionStatus::InReview]);
    }
}
