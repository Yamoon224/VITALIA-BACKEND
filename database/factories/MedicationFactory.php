<?php

namespace Database\Factories;

use App\Models\Medication;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Medication> */
class MedicationFactory extends Factory
{
    protected $model = Medication::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'dci' => fake()->unique()->word(),
            'form' => 'comprimé',
            'standard_dosage' => '1 comprimé, 3 fois par jour',
            'max_duration_days' => 7,
            'is_restricted' => false,
            'allergen_tags' => [],
            'contraindications' => [],
            'interacts_with' => [],
        ];
    }
}
