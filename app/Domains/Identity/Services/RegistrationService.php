<?php

namespace App\Domains\Identity\Services;

use App\Domains\Identity\Contracts\UserRepositoryContract;
use App\Domains\Identity\Enums\UserRole;
use App\Domains\Shared\Contracts\TransactionManagerContract;
use App\Models\User;

/**
 * Inscription par téléphone (OTP) ou e-mail (section 5.1).
 *
 * La vérification OTP elle-même (envoi, contrôle du code) relève du domaine
 * `Notification` ; ce service se limite à la création du compte une fois
 * l'identité confirmée, avec le rôle patient par défaut.
 */
class RegistrationService
{
    public function __construct(
        private readonly UserRepositoryContract $users,
        private readonly TransactionManagerContract $transactions,
    ) {}

    /** @param  array{name: string, email?: string, phone?: string, password: string, locale?: string}  $attributes */
    public function register(array $attributes): User
    {
        return $this->transactions->run(function () use ($attributes): User {
            $user = $this->users->create($attributes);
            $user->assignRole(UserRole::Patient->value);

            return $user;
        });
    }
}
