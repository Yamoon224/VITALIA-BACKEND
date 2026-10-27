<?php

use App\Domains\Prescription\Enums\PrescriptionStatus;
use App\Models\Prescription;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lists only the authenticated patient own prescriptions, most recent first', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $otherPatient = User::factory()->create();
    $otherPatient->assignRole('patient');

    $older = Prescription::query()->create([
        'user_id' => $patient->id,
        'status' => PrescriptionStatus::Draft,
        'items' => [['dci' => 'Paracétamol', 'dosage' => '1g', 'duration_days' => 3]],
    ]);

    $newer = Prescription::query()->create([
        'user_id' => $patient->id,
        'status' => PrescriptionStatus::Signed,
        'items' => [['dci' => 'Ibuprofène', 'dosage' => '400mg', 'duration_days' => 5]],
    ]);

    Prescription::query()->create([
        'user_id' => $otherPatient->id,
        'status' => PrescriptionStatus::Signed,
        'items' => [],
    ]);

    $response = $this->actingAs($patient)
        ->getJson('/api/v1/prescriptions')
        ->assertOk();

    $response->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $newer->id)
        ->assertJsonPath('data.1.id', $older->id);
});

it('returns an empty list for a patient with no prescriptions', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $this->actingAs($patient)
        ->getJson('/api/v1/prescriptions')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});
