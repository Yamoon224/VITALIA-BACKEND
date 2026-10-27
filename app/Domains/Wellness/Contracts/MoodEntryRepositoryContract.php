<?php

namespace App\Domains\Wellness\Contracts;

use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Support\Collection;

interface MoodEntryRepositoryContract
{
    public function record(User $user, int $moodLevel, ?string $note = null): MoodEntry;

    /**
     * Les N dernières entrées, de la plus récente à la plus ancienne.
     *
     * @return Collection<int, MoodEntry>
     */
    public function recentFor(User $user, int $limit = 7): Collection;
}
