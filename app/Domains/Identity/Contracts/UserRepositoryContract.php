<?php

namespace App\Domains\Identity\Contracts;

use App\Models\User;

interface UserRepositoryContract
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findByPhone(string $phone): ?User;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): User;
}
