<?php

use App\Models\PractitionerProfile;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets an administrator list practitioners pending verification', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('administrator');

    $pending = PractitionerProfile::factory()->unverified()->create();
    PractitionerProfile::factory()->create();

    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/practitioners/pending')
        ->assertOk();

    $response->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.user_id', $pending->user_id);
});

it('lets an administrator verify a practitioner diploma', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('administrator');

    $profile = PractitionerProfile::factory()->unverified()->create();

    $this->actingAs($admin)
        ->postJson("/api/v1/admin/practitioners/{$profile->user_id}/verify")
        ->assertOk()
        ->assertJsonPath('data.verified', true);

    expect($profile->fresh()->verified_at)->not->toBeNull();
});

it('lets an administrator revoke a practitioner verification', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('administrator');

    $profile = PractitionerProfile::factory()->create();

    $this->actingAs($admin)
        ->postJson("/api/v1/admin/practitioners/{$profile->user_id}/revoke")
        ->assertOk()
        ->assertJsonPath('data.verified', false);

    expect($profile->fresh()->verified_at)->toBeNull();
});

it('forbids a clinical referent from managing practitioner verification', function (): void {
    $referent = User::factory()->create();
    $referent->assignRole('clinical_referent');

    $profile = PractitionerProfile::factory()->unverified()->create();

    $this->actingAs($referent)
        ->postJson("/api/v1/admin/practitioners/{$profile->user_id}/verify")
        ->assertForbidden();
});
