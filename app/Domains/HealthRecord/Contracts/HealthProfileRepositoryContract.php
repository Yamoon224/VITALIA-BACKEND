<?php

namespace App\Domains\HealthRecord\Contracts;

use App\Models\HealthProfile;
use App\Models\User;

interface HealthProfileRepositoryContract
{
    public function forUser(User $user): ?HealthProfile;

    /** @param  array<string, mixed>  $attributes */
    public function updateFor(User $user, array $attributes): HealthProfile;
}
