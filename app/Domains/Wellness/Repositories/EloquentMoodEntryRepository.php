<?php

namespace App\Domains\Wellness\Repositories;

use App\Domains\Wellness\Contracts\MoodEntryRepositoryContract;
use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Support\Collection;

class EloquentMoodEntryRepository implements MoodEntryRepositoryContract
{
    public function record(User $user, int $moodLevel, ?string $note = null): MoodEntry
    {
        return MoodEntry::query()->create([
            'user_id' => $user->id,
            'mood_level' => $moodLevel,
            'note' => $note,
            'recorded_at' => now(),
        ]);
    }

    /** @return Collection<int, MoodEntry> */
    public function recentFor(User $user, int $limit = 7): Collection
    {
        return MoodEntry::query()
            ->where('user_id', $user->id)
            ->latest('recorded_at')
            ->limit($limit)
            ->get();
    }
}
