<?php

namespace App\Domains\Practitioner\Repositories;

use App\Domains\Practitioner\Contracts\PractitionerDirectoryRepositoryContract;
use App\Models\PractitionerProfile;
use Illuminate\Support\Collection;

class EloquentPractitionerDirectoryRepository implements PractitionerDirectoryRepositoryContract
{
    /** @return Collection<int, PractitionerProfile> */
    public function verified(?string $specialty = null): Collection
    {
        return PractitionerProfile::query()
            ->whereNotNull('verified_at')
            ->when($specialty !== null, fn ($query) => $query->where('specialty', $specialty))
            ->get();
    }

    public function find(int $userId): ?PractitionerProfile
    {
        return PractitionerProfile::query()->where('user_id', $userId)->first();
    }
}
