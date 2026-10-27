<?php

use App\Models\AuditLog;
use App\Models\Prescription;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets an administrator list the most recent audit log entries', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('administrator');
    $practitioner = User::factory()->create();
    $practitioner->assignRole('practitioner');

    AuditLog::query()->create([
        'actor_id' => $practitioner->id,
        'action' => 'prescription.signed',
        'auditable_type' => Prescription::class,
        'auditable_id' => 1,
        'context' => ['patient_id' => 42],
        'created_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/audit-logs')
        ->assertOk();

    $response->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.action', 'prescription.signed')
        ->assertJsonPath('data.0.actor_name', $practitioner->name)
        ->assertJsonPath('data.0.auditable_type', 'Prescription');
});

it('filters audit log entries by action', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('administrator');

    AuditLog::query()->create(['action' => 'prescription.signed', 'context' => [], 'created_at' => now()]);
    AuditLog::query()->create(['action' => 'emergency.detected', 'context' => [], 'created_at' => now()]);

    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/audit-logs?action=emergency.detected')
        ->assertOk();

    $response->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.action', 'emergency.detected');
});

it('forbids a clinical referent from reading the audit log', function (): void {
    $referent = User::factory()->create();
    $referent->assignRole('clinical_referent');

    $this->actingAs($referent)
        ->getJson('/api/v1/admin/audit-logs')
        ->assertForbidden();
});

it('forbids a patient from reading the audit log', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $this->actingAs($patient)
        ->getJson('/api/v1/admin/audit-logs')
        ->assertForbidden();
});
