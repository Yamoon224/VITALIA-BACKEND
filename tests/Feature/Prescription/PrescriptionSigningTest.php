<?php

use App\Domains\Prescription\Enums\PrescriptionStatus;
use App\Models\Prescription;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets a practitioner sign a prescription awaiting review', function (): void {
    $practitioner = User::factory()->create();
    $practitioner->assignRole('practitioner');

    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $prescription = Prescription::query()->create([
        'user_id' => $patient->id,
        'status' => PrescriptionStatus::InReview,
        'items' => [['dci' => 'Paracétamol', 'dosage' => '1g', 'duration_days' => 3]],
        'safety_alerts' => [],
    ]);

    $this->actingAs($practitioner)
        ->postJson("/api/v1/practitioner/prescriptions/{$prescription->id}/sign")
        ->assertOk()
        ->assertJson(['data' => ['status' => 'signed']]);

    $prescription->refresh();

    expect($prescription->status)->toBe(PrescriptionStatus::Signed)
        ->and($prescription->practitioner_id)->toBe($practitioner->id)
        ->and($prescription->signature_hash)->not->toBeNull();

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'prescription.signed',
        'actor_id' => $practitioner->id,
    ]);
});

it('forbids a patient from reaching the practitioner review endpoint', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $prescription = Prescription::query()->create([
        'user_id' => $patient->id,
        'status' => PrescriptionStatus::InReview,
        'items' => [],
    ]);

    $this->actingAs($patient)
        ->postJson("/api/v1/practitioner/prescriptions/{$prescription->id}/sign")
        ->assertForbidden();
});

it('refuses to sign a draft that has not been submitted for review', function (): void {
    $practitioner = User::factory()->create();
    $practitioner->assignRole('practitioner');

    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $prescription = Prescription::query()->create([
        'user_id' => $patient->id,
        'status' => PrescriptionStatus::Draft,
        'items' => [],
    ]);

    $this->actingAs($practitioner)
        ->postJson("/api/v1/practitioner/prescriptions/{$prescription->id}/sign")
        ->assertStatus(409)
        ->assertJson(['error_code' => 'invalid_prescription_transition']);
});
