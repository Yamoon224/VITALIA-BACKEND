<?php

namespace App\Domains\Wellness\Services;

use App\Domains\Wellness\Contracts\MoodEntryRepositoryContract;
use App\Domains\Wellness\Contracts\WellnessGoalRepositoryContract;
use App\Domains\Wellness\Enums\WellnessGoalType;
use App\Domains\Wellness\Events\ConcerningMoodTrendDetected;
use App\Models\MoodEntry;
use App\Models\User;
use App\Models\WellnessGoal;
use Illuminate\Support\Collection;

class WellnessService
{
    /**
     * Seuil déterministe et volontairement simple au stade du socle : trois
     * entrées consécutives d'humeur basse (≤ 2 sur 5) déclenchent le signal.
     * À affiner avec le comité clinique (section 3.1, « gouvernance
     * médicale ») avant mise en production.
     */
    private const int CONCERNING_STREAK_LENGTH = 3;

    private const int CONCERNING_MOOD_THRESHOLD = 2;

    public function __construct(
        private readonly WellnessGoalRepositoryContract $goals,
        private readonly MoodEntryRepositoryContract $moodEntries,
    ) {}

    /** @return Collection<int, WellnessGoal> */
    public function goalsFor(User $user): Collection
    {
        return $this->goals->forUser($user);
    }

    public function setGoal(User $user, WellnessGoalType $type, float $target): WellnessGoal
    {
        return $this->goals->setTarget($user, $type, $target);
    }

    public function updateProgress(User $user, WellnessGoalType $type, float $value): ?WellnessGoal
    {
        return $this->goals->updateProgress($user, $type, $value);
    }

    public function logMood(User $user, int $moodLevel, ?string $note = null): MoodEntry
    {
        $entry = $this->moodEntries->record($user, $moodLevel, $note);

        $this->checkTrend($user);

        return $entry;
    }

    private function checkTrend(User $user): void
    {
        $recent = $this->moodEntries->recentFor($user, self::CONCERNING_STREAK_LENGTH);

        if ($recent->count() < self::CONCERNING_STREAK_LENGTH) {
            return;
        }

        $allLow = $recent->every(fn (MoodEntry $entry) => $entry->mood_level <= self::CONCERNING_MOOD_THRESHOLD);

        if ($allLow) {
            ConcerningMoodTrendDetected::dispatch($user, (float) $recent->avg('mood_level'));
        }
    }
}
