<?php

namespace Database\Factories;

use App\Models\ExaminationResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ExaminationResult> */
class ExaminationResultFactory extends Factory
{
    protected $model = ExaminationResult::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'parameter' => 'Glycémie à jeun',
            'value' => fake()->randomFloat(2, 0.7, 1.3),
            'unit' => 'g/L',
            'reference_low' => 0.7,
            'reference_high' => 1.1,
            'is_abnormal' => false,
            'recorded_at' => now(),
        ];
    }
}
