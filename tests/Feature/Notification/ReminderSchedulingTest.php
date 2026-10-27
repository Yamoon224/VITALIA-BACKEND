<?php

use App\Domains\Prescription\Enums\PrescriptionStatus;
use App\Models\Prescription;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('schedules a medication reminder per item when a prescription is signed', function (): void {
    $practitioner = User::factory()->create();
    $practitioner->assignRole('practitioner');

    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $prescription = Prescription::query()->create([
        'user_id' => $patient->id,
        'status' => PrescriptionStatus::InReview,
        'items' => [
            ['dci' => 'Paracétamol', 'dosage' => '1g', 'duration_days' => 3],
            ['dci' => 'Ibuprofène', 'dosage' => '400mg', 'duration_days' => 5],
        ],
    ]);

    $this->actingAs($practitioner)->postJson("/api/v1/practitioner/prescriptions/{$prescription->id}/sign");

    $this->assertDatabaseCount('reminders', 2);
    $this->assertDatabaseHas('reminders', ['user_id' => $patient->id, 'prescription_id' => $prescription->id, 'type' => 'medication']);

    $this->actingAs($patient)
        ->getJson('/api/v1/reminders')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('lets a patient update their notification preferences', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $this->actingAs($patient)
        ->putJson('/api/v1/notification-preferences', ['sms_enabled' => true, 'push_enabled' => false])
        // 201 : première écriture pour cet utilisateur (`updateOrCreate`),
        // Laravel marque la ressource comme nouvellement créée.
        ->assertCreated()
        ->assertJson(['data' => ['sms_enabled' => true, 'push_enabled' => false]]);
});
