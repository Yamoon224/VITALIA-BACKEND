<?php

namespace App\Domains\Notification\Repositories;

use App\Domains\Notification\Contracts\ReminderRepositoryContract;
use App\Domains\Notification\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Collection;

class EloquentReminderRepository implements ReminderRepositoryContract
{
    public function schedule(
        User $user,
        ReminderType $type,
        string $message,
        DateTimeInterface $scheduledAt,
        ?int $prescriptionId = null,
    ): Reminder {
        return Reminder::query()->create([
            'user_id' => $user->id,
            'prescription_id' => $prescriptionId,
            'type' => $type,
            'message' => $message,
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /** @return Collection<int, Reminder> */
    public function upcomingFor(User $user): Collection
    {
        return Reminder::query()
            ->where('user_id', $user->id)
            ->whereNull('sent_at')
            ->orderBy('scheduled_at')
            ->get();
    }

    public function markSent(Reminder $reminder): Reminder
    {
        $reminder->sent_at = now();
        $reminder->save();

        return $reminder;
    }
}
