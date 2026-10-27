<?php

namespace App\Domains\Wellness\Repositories;

use App\Domains\Wellness\Contracts\WellnessGoalRepositoryContract;
use App\Domains\Wellness\Enums\WellnessGoalType;
use App\Models\User;
use App\Models\WellnessGoal;
use Illuminate\Support\Collection;

class EloquentWellnessGoalRepository implements WellnessGoalRepositoryContract
{
    /** @return Collection<int, WellnessGoal> */
    public function forUser(User $user): Collection
    {
        return WellnessGoal::query()->where('user_id', $user->id)->get();
    }

    public function setTarget(User $user, WellnessGoalType $type, float $target): WellnessGoal
    {
        return WellnessGoal::query()->updateOrCreate(
            ['user_id' => $user->id, 'type' => $type],
            ['target_value' => $target],
        );
    }

    public function updateProgress(User $user, WellnessGoalType $type, float $value): ?WellnessGoal
    {
        $goal = WellnessGoal::query()->where('user_id', $user->id)->where('type', $type)->first();

        if ($goal === null) {
            return null;
        }

        $goal->current_value = $value;
        $goal->save();

        return $goal;
    }
}
