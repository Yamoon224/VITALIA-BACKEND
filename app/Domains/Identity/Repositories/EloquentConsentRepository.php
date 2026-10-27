<?php

namespace App\Domains\Identity\Repositories;

use App\Domains\Identity\Contracts\ConsentRepositoryContract;
use App\Domains\Identity\Enums\ConsentType;
use App\Models\Consent;
use App\Models\User;
use Illuminate\Support\Collection;

class EloquentConsentRepository implements ConsentRepositoryContract
{
    /** @return Collection<int, Consent> */
    public function currentFor(User $user): Collection
    {
        return Consent::query()
            ->where('user_id', $user->id)
            ->latest('granted_at')
            ->get()
            ->unique('type');
    }

    public function isGranted(User $user, ConsentType $type): bool
    {
        $latest = Consent::query()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->latest('granted_at')
            ->first();

        return $latest !== null && $latest->granted && $latest->revoked_at === null;
    }

    public function record(User $user, ConsentType $type, bool $granted): Consent
    {
        return Consent::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'granted' => $granted,
            'granted_at' => $granted ? now() : null,
            'revoked_at' => $granted ? null : now(),
        ]);
    }
}
