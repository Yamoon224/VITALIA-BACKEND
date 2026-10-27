<?php

namespace App\Domains\HealthRecord\Repositories;

use App\Domains\HealthRecord\Contracts\HealthProfileRepositoryContract;
use App\Models\HealthProfile;
use App\Models\User;

class EloquentHealthProfileRepository implements HealthProfileRepositoryContract
{
    public function forUser(User $user): ?HealthProfile
    {
        return HealthProfile::query()->where('user_id', $user->id)->first();
    }

    public function updateFor(User $user, array $attributes): HealthProfile
    {
        return HealthProfile::query()->updateOrCreate(['user_id' => $user->id], $attributes);
    }
}
