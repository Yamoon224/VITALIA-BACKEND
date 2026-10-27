<?php

use App\Domains\Examination\Enums\ExaminationRequestStatus;
use App\Models\ExaminationRequest;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets a practitioner validate a draft examination request', function (): void {
    $practitioner = User::factory()->create();
    $practitioner->assignRole('practitioner');

    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $examination = ExaminationRequest::query()->create([
        'user_id' => $patient->id,
        'status' => ExaminationRequestStatus::Draft,
        'items' => [['name' => 'Numération formule sanguine', 'reason' => 'Fatigue']],
    ]);

    $this->actingAs($practitioner)
        ->postJson("/api/v1/practitioner/examinations/{$examination->id}/validate")
        ->assertOk()
        ->assertJson(['data' => ['status' => 'validated']]);

    expect($examination->fresh()->status)->toBe(ExaminationRequestStatus::Validated);
});

it('rejects validation by a non-practitioner account', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $examination = ExaminationRequest::query()->create([
        'user_id' => $patient->id,
        'status' => ExaminationRequestStatus::Draft,
        'items' => [],
    ]);

    $this->actingAs($patient)
        ->postJson("/api/v1/practitioner/examinations/{$examination->id}/validate")
        ->assertForbidden();
});
