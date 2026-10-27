<?php

namespace App\Domains\Identity\Repositories;

use App\Domains\Identity\Contracts\UserRepositoryContract;
use App\Models\User;

class EloquentUserRepository implements UserRepositoryContract
{
    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return User::query()->where('phone', $phone)->first();
    }

    public function create(array $attributes): User
    {
        return User::query()->create($attributes);
    }
}
