<?php

use App\Domains\Wellness\Events\ConcerningMoodTrendDetected;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets a patient set a wellness goal and log their mood', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $this->actingAs($user)
        ->postJson('/api/v1/wellness/goals', ['type' => 'steps', 'target_value' => 8000])
        ->assertCreated()
        ->assertJson(['data' => ['type' => 'steps', 'target_value' => 8000]]);

    $this->actingAs($user)
        ->postJson('/api/v1/wellness/mood', ['mood_level' => 4])
        ->assertCreated()
        ->assertJson(['data' => ['mood_level' => 4]]);
});

it('detects a concerning mood trend after three consecutive low entries', function (): void {
    Event::fake([ConcerningMoodTrendDetected::class]);

    $user = User::factory()->create();
    $user->assignRole('patient');

    foreach ([2, 1, 2] as $level) {
        $this->actingAs($user)->postJson('/api/v1/wellness/mood', ['mood_level' => $level]);
    }

    Event::assertDispatched(ConcerningMoodTrendDetected::class);
});

it('does not trigger a concerning trend on a single low entry', function (): void {
    Event::fake([ConcerningMoodTrendDetected::class]);

    $user = User::factory()->create();
    $user->assignRole('patient');

    $this->actingAs($user)->postJson('/api/v1/wellness/mood', ['mood_level' => 1]);

    Event::assertNotDispatched(ConcerningMoodTrendDetected::class);
});
