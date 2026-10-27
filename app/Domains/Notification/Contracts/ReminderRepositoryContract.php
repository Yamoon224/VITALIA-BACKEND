<?php

namespace App\Domains\Notification\Contracts;

use App\Domains\Notification\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Support\Collection;

interface ReminderRepositoryContract
{
    public function schedule(
        User $user,
        ReminderType $type,
        string $message,
        \DateTimeInterface $scheduledAt,
        ?int $prescriptionId = null,
    ): Reminder;

    /** @return Collection<int, Reminder> */
    public function upcomingFor(User $user): Collection;

    public function markSent(Reminder $reminder): Reminder;
}
