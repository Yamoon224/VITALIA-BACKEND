<?php

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\ReminderRepositoryContract;
use App\Domains\Notification\Enums\ReminderType;
use App\Models\Prescription;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Rappels de prise de médicaments conformes à l'ordonnance signée
 * (section 5.10). Programmés localement sur l'appareil côté mobile
 * (section 5.14) ; ce service est la source de vérité serveur qui alimente
 * cette programmation locale et les notifications push de secours.
 */
class ReminderService
{
    public function __construct(private readonly ReminderRepositoryContract $reminders) {}

    /** Un rappel par ligne d'ordonnance, à la prochaine prise (section 5.10). */
    public function scheduleForPrescription(Prescription $prescription): void
    {
        /** @var User $patient */
        $patient = $prescription->patient;

        foreach ($prescription->items as $item) {
            $label = $item['dci'] ?? 'traitement prescrit';

            $this->reminders->schedule(
                $patient,
                ReminderType::Medication,
                "Prise de {$label} selon l'ordonnance.",
                now()->addHours(8),
                $prescription->id,
            );
        }
    }

    /** @return Collection<int, Reminder> */
    public function upcomingFor(User $user): Collection
    {
        return $this->reminders->upcomingFor($user);
    }
}
