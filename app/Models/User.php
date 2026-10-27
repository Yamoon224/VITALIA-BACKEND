<?php

namespace App\Models;

use App\Domains\Identity\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Compte unique pour tous les profils du système (section 4) : le rôle
 * (patient, aidant, praticien, référent clinique, support, administrateur)
 * est porté par `spatie/laravel-permission`, jamais par une colonne figée,
 * pour permettre des permissions fines sans migration à chaque évolution.
 */
#[Fillable(['name', 'email', 'phone', 'password', 'locale'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasDefaultRole(UserRole $role): bool
    {
        return $this->hasRole($role->value);
    }
}
