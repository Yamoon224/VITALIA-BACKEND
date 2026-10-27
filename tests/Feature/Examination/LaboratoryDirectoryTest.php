<?php

use App\Models\Laboratory;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lists laboratories for an authenticated patient', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    Laboratory::factory()->count(3)->create();

    $this->actingAs($patient)
        ->getJson('/api/v1/laboratories')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('filters laboratories by city', function (): void {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    Laboratory::factory()->create(['city' => 'Abidjan']);
    Laboratory::factory()->create(['city' => 'Bouaké']);

    $this->actingAs($patient)
        ->getJson('/api/v1/laboratories?city=Abidjan')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.city', 'Abidjan');
});
