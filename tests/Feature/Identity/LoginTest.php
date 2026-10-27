<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('logs a practitioner in with valid credentials', function (): void {
    $practitioner = User::factory()->create(['password' => Hash::make('secret-password')]);
    $practitioner->assignRole('practitioner');

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $practitioner->email,
        'password' => 'secret-password',
    ])->assertOk();

    expect($response->json('token'))->not->toBeNull()
        ->and($response->json('user.email'))->toBe($practitioner->email);
});

it('rejects an invalid password', function (): void {
    $user = User::factory()->create(['password' => Hash::make('secret-password')]);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])
        ->assertStatus(401)
        ->assertJson(['error_code' => 'invalid_credentials']);
});
