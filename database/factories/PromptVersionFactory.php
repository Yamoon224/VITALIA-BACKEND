<?php

namespace Database\Factories;

use App\Domains\Assistant\Enums\PromptVersionStatus;
use App\Models\PromptVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PromptVersion> */
class PromptVersionFactory extends Factory
{
    protected $model = PromptVersion::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'block_key' => 'tone',
            'version' => 1,
            'content' => fake()->sentence(),
            'status' => PromptVersionStatus::Draft,
        ];
    }
}
