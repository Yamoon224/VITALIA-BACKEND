<?php

namespace Database\Factories;

use App\Domains\HealthRecord\Enums\MemoryEntryStatus;
use App\Models\AgentMemoryEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AgentMemoryEntry> */
class AgentMemoryEntryFactory extends Factory
{
    protected $model = AgentMemoryEntry::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'conversation_id' => null,
            'content' => fake()->sentence(),
            'status' => MemoryEntryStatus::Proposed,
        ];
    }
}
