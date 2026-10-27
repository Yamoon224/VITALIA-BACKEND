<?php

namespace App\Domains\Identity\Contracts;

use App\Domains\Identity\Enums\ConsentType;
use App\Models\Consent;
use App\Models\User;
use Illuminate\Support\Collection;

interface ConsentRepositoryContract
{
    /** @return Collection<int, Consent> */
    public function currentFor(User $user): Collection;

    public function isGranted(User $user, ConsentType $type): bool;

    public function record(User $user, ConsentType $type, bool $granted): Consent;
}
