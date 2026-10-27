<?php

namespace Database\Factories;

use App\Domains\Examination\Enums\ExaminationRequestStatus;
use App\Models\ExaminationRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ExaminationRequest> */
class ExaminationRequestFactory extends Factory
{
    protected $model = ExaminationRequest::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => ExaminationRequestStatus::Draft,
            'items' => [['name' => 'Numération formule sanguine', 'reason' => 'Fatigue persistante']],
        ];
    }
}
