<?php

namespace App\Domains\Notification\Repositories;

use App\Domains\Notification\Contracts\NotificationPreferenceRepositoryContract;
use App\Models\NotificationPreference;
use App\Models\User;

class EloquentNotificationPreferenceRepository implements NotificationPreferenceRepositoryContract
{
    public function forUser(User $user): NotificationPreference
    {
        return NotificationPreference::query()->firstOrCreate(['user_id' => $user->id]);
    }

    public function update(User $user, array $attributes): NotificationPreference
    {
        return NotificationPreference::query()->updateOrCreate(['user_id' => $user->id], $attributes);
    }
}
