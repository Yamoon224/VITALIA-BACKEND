<?php

use App\Domains\Examination\Enums\ExaminationRequestStatus;
use App\Models\ExaminationRequest;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lists only the authenticated patient own examination requests, most recent first', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $otherPatient = User::factory()->create();
    $otherPatient->assignRole('patient');

    $older = ExaminationRequest::query()->create([
        'user_id' => $patient->id,
        'status' => ExaminationRequestStatus::Draft,
        'items' => [['name' => 'Numération formule sanguine', 'reason' => 'Fatigue', 'prep_instructions' => '']],
    ]);

    $newer = ExaminationRequest::query()->create([
        'user_id' => $patient->id,
        'status' => ExaminationRequestStatus::Validated,
        'items' => [['name' => 'Glycémie à jeun', 'reason' => 'Suivi', 'prep_instructions' => 'À jeun']],
    ]);

    ExaminationRequest::query()->create([
        'user_id' => $otherPatient->id,
        'status' => ExaminationRequestStatus::Validated,
        'items' => [],
    ]);

    $response = $this->actingAs($patient)
        ->getJson('/api/v1/examination-requests')
        ->assertOk();

    $response->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $newer->id)
        ->assertJsonPath('data.1.id', $older->id);
});

it('returns an empty list for a patient with no examination requests', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $this->actingAs($patient)
        ->getJson('/api/v1/examination-requests')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});
