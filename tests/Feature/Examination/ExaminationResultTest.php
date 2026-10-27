<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('flags a result outside the reference range as abnormal and logs it', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $response = $this->actingAs($user)
        ->postJson('/api/v1/examination-results', [
            'parameter' => 'Glycémie à jeun',
            'value' => 1.8,
            'unit' => 'g/L',
            'reference_low' => 0.7,
            'reference_high' => 1.1,
        ])
        ->assertCreated();

    $response->assertJson(['data' => ['is_abnormal' => true]]);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'examination_result.critical',
        'actor_id' => $user->id,
    ]);
});

it('does not flag a result within the reference range', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $this->actingAs($user)
        ->postJson('/api/v1/examination-results', [
            'parameter' => 'Glycémie à jeun',
            'value' => 0.9,
            'unit' => 'g/L',
            'reference_low' => 0.7,
            'reference_high' => 1.1,
        ])
        ->assertCreated()
        ->assertJson(['data' => ['is_abnormal' => false]]);
});
