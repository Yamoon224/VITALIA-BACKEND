<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets a patient update their own health profile', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $this->actingAs($user)
        ->putJson('/api/v1/health-profile', [
            'birth_year' => 1990,
            'sex' => 'female',
            'allergies' => ['pénicilline'],
            'is_pregnant' => true,
        ])
        // 201 : Laravel marque la ressource comme nouvellement créée
        // (`wasRecentlyCreated`), ce premier enregistrement passe par
        // `updateOrCreate()` côté serveur (section 5.1).
        ->assertCreated()
        ->assertJson([
            'data' => [
                'birth_year' => 1990,
                'sex' => 'female',
                'allergies' => ['pénicilline'],
                'is_pregnant' => true,
            ],
        ]);

    $this->assertDatabaseHas('health_profiles', ['user_id' => $user->id, 'birth_year' => 1990]);
});

it('returns an empty profile shape for a patient without one yet', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $this->actingAs($user)
        ->getJson('/api/v1/health-profile')
        ->assertOk()
        ->assertJson(['data' => ['birth_year' => null, 'allergies' => []]]);
});
