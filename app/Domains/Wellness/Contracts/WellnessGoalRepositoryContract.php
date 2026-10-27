<?php

namespace App\Domains\Wellness\Contracts;

use App\Domains\Wellness\Enums\WellnessGoalType;
use App\Models\User;
use App\Models\WellnessGoal;
use Illuminate\Support\Collection;

interface WellnessGoalRepositoryContract
{
    /** @return Collection<int, WellnessGoal> */
    public function forUser(User $user): Collection;

    public function setTarget(User $user, WellnessGoalType $type, float $target): WellnessGoal;

    public function updateProgress(User $user, WellnessGoalType $type, float $value): ?WellnessGoal;
}
