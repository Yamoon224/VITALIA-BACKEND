<?php

namespace App\Domains\Practitioner\Contracts;

use App\Models\PractitionerProfile;
use Illuminate\Support\Collection;

interface PractitionerDirectoryRepositoryContract
{
    /**
     * Annuaire de praticiens vérifiés (section 5.11).
     *
     * @return Collection<int, PractitionerProfile>
     */
    public function verified(?string $specialty = null): Collection;

    public function find(int $userId): ?PractitionerProfile;
}
