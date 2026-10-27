<?php

namespace Database\Factories;

use App\Domains\Examination\Enums\LaboratoryType;
use App\Models\Laboratory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Laboratory> */
class LaboratoryFactory extends Factory
{
    protected $model = Laboratory::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Laboratoire',
            'type' => LaboratoryType::Laboratory,
            'city' => fake()->city(),
            'address' => fake()->address(),
        ];
    }
}
