<?php

use App\Domains\Assistant\Enums\PromptVersionStatus;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets an administrator draft a new prompt version', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('administrator');

    $this->actingAs($admin)
        ->postJson('/api/v1/admin/prompts', [
            'block_key' => 'safety_rules',
            'content' => 'Nouvelle règle de sécurité.',
        ])
        ->assertCreated()
        ->assertJson(['data' => [
            'block_key' => 'safety_rules',
            'version' => 1,
            'status' => 'draft',
        ]]);

    $this->assertDatabaseHas('prompt_versions', [
        'block_key' => 'safety_rules',
        'version' => 1,
        'status' => PromptVersionStatus::Draft,
    ]);
});

it('lets a clinical referent publish a drafted version and archives the previous one', function (): void {
    $referent = User::factory()->create();
    $referent->assignRole('clinical_referent');

    $firstVersionId = $this->actingAs($referent)
        ->postJson('/api/v1/admin/prompts', ['block_key' => 'tone', 'content' => 'Ton v1'])
        ->json('data.id');

    $this->actingAs($referent)
        ->postJson("/api/v1/admin/prompts/{$firstVersionId}/publish")
        ->assertOk()
        ->assertJson(['data' => ['status' => 'published']]);

    $secondVersionId = $this->actingAs($referent)
        ->postJson('/api/v1/admin/prompts', ['block_key' => 'tone', 'content' => 'Ton v2'])
        ->json('data.id');

    $this->actingAs($referent)
        ->postJson("/api/v1/admin/prompts/{$secondVersionId}/publish")
        ->assertOk();

    $history = $this->actingAs($referent)
        ->getJson('/api/v1/admin/prompts/tone/history')
        ->json('data');

    expect(collect($history)->firstWhere('id', $firstVersionId)['status'])->toBe('archived')
        ->and(collect($history)->firstWhere('id', $secondVersionId)['status'])->toBe('published');
});

it('rejects an unknown block key', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('administrator');

    $this->actingAs($admin)
        ->postJson('/api/v1/admin/prompts', ['block_key' => 'not_a_real_block', 'content' => 'x'])
        ->assertStatus(422);
});

it('forbids a support account from managing prompts', function (): void {
    $support = User::factory()->create();
    $support->assignRole('support');

    $this->actingAs($support)
        ->getJson('/api/v1/admin/prompts/tone/history')
        ->assertForbidden();
});
