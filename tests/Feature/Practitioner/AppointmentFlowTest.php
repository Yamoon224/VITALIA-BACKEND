<?php

use App\Models\PractitionerProfile;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lists only verified practitioners in the directory', function (): void {
    $verified = User::factory()->create();
    $verified->assignRole('practitioner');
    PractitionerProfile::factory()->for($verified)->create();

    $unverified = User::factory()->create();
    $unverified->assignRole('practitioner');
    PractitionerProfile::factory()->for($unverified)->unverified()->create();

    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $response = $this->actingAs($patient)->getJson('/api/v1/practitioners')->assertOk();

    $ids = collect($response->json('data'))->pluck('user_id');

    expect($ids)->toContain($verified->id)->not->toContain($unverified->id);
});

it('lets a patient request an appointment and a practitioner confirm it', function (): void {
    $practitioner = User::factory()->create();
    $practitioner->assignRole('practitioner');
    PractitionerProfile::factory()->for($practitioner)->create();

    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $appointment = $this->actingAs($patient)
        ->postJson('/api/v1/appointments', [
            'practitioner_id' => $practitioner->id,
            'scheduled_at' => now()->addDay()->toIso8601String(),
            'type' => 'teleconsultation',
        ])
        ->assertCreated()
        ->json('data');

    $this->actingAs($practitioner)
        ->postJson("/api/v1/practitioner/appointments/{$appointment['id']}/confirm")
        ->assertOk()
        ->assertJson(['data' => ['status' => 'confirmed']]);
});

it('refuses to confirm an appointment that is not awaiting confirmation', function (): void {
    $practitioner = User::factory()->create();
    $practitioner->assignRole('practitioner');
    PractitionerProfile::factory()->for($practitioner)->create();

    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $appointment = $this->actingAs($patient)
        ->postJson('/api/v1/appointments', [
            'practitioner_id' => $practitioner->id,
            'scheduled_at' => now()->addDay()->toIso8601String(),
            'type' => 'teleconsultation',
        ])
        ->json('data');

    $this->actingAs($practitioner)->postJson("/api/v1/practitioner/appointments/{$appointment['id']}/confirm");

    $this->actingAs($practitioner)
        ->postJson("/api/v1/practitioner/appointments/{$appointment['id']}/confirm")
        ->assertStatus(409)
        ->assertJson(['error_code' => 'invalid_appointment_transition']);
});
