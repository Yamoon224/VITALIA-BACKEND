<?php

namespace App\Domains\Notification\Contracts;

use App\Models\NotificationPreference;
use App\Models\User;

interface NotificationPreferenceRepositoryContract
{
    public function forUser(User $user): NotificationPreference;

    /** @param  array<string, mixed>  $attributes */
    public function update(User $user, array $attributes): NotificationPreference;
}
