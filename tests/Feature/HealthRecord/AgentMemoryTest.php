<?php

use App\Domains\HealthRecord\Contracts\HealthContextProviderContract;
use App\Domains\HealthRecord\Enums\MemoryEntryStatus;
use App\Domains\HealthRecord\Services\AgentMemoryService;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets a patient accept a proposed memory entry', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $entry = app(AgentMemoryService::class)->propose($user, 'Allergie au pollen mentionnée en conversation.');

    $this->actingAs($user)
        ->postJson("/api/v1/agent-memory/{$entry->id}/accept")
        ->assertOk()
        ->assertJson(['data' => ['status' => 'accepted']]);

    expect($entry->fresh()->status)->toBe(MemoryEntryStatus::Accepted);
});

it('does not let a patient review another patient\'s proposed memory', function (): void {
    $owner = User::factory()->create();
    $owner->assignRole('patient');
    $intruder = User::factory()->create();
    $intruder->assignRole('patient');

    $entry = app(AgentMemoryService::class)->propose($owner, 'Information sensible.');

    $this->actingAs($intruder)
        ->postJson("/api/v1/agent-memory/{$entry->id}/accept")
        ->assertNotFound();
});

it('includes accepted entries in the health context summary used by the agent', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $memory = app(AgentMemoryService::class);
    $entry = $memory->propose($user, 'Pratique le jeûne intermittent.');
    $memory->accept($entry);

    $summary = app(HealthContextProviderContract::class)->summaryFor($user);

    expect($summary)->toContain('Pratique le jeûne intermittent.');
});
