<?php

use App\Domains\Conversation\Enums\MessageFeedback;
use App\Models\EmergencyDetection;
use App\Models\Message;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets an administrator read the supervision stats', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('administrator');

    EmergencyDetection::factory()->create();
    Message::factory()->create(['feedback' => MessageFeedback::Concerning]);

    $this->actingAs($admin)
        ->getJson('/api/v1/admin/supervision/stats')
        ->assertOk()
        ->assertJsonPath('data.emergency_detections_today', 1)
        ->assertJsonPath('data.concerning_feedback_count', 1);
});

it('lets a clinical referent read flagged messages', function (): void {
    $referent = User::factory()->create();
    $referent->assignRole('clinical_referent');

    Message::factory()->create(['feedback' => MessageFeedback::Concerning]);
    Message::factory()->create(['feedback' => MessageFeedback::Useful]);

    $response = $this->actingAs($referent)
        ->getJson('/api/v1/admin/supervision/flagged-messages')
        ->assertOk();

    $response->assertJsonCount(1, 'data');
});

it('forbids a patient from reading the supervision dashboard', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $this->actingAs($patient)
        ->getJson('/api/v1/admin/supervision/stats')
        ->assertForbidden();
});
