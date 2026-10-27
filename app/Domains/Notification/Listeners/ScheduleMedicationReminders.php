<?php

namespace App\Domains\Notification\Listeners;

use App\Domains\Notification\Services\ReminderService;
use App\Domains\Prescription\Events\PrescriptionSigned;

/**
 * Observateur (section 10.2) : `Prescription` signe et journalise son
 * propre évènement ; `Notification` programme les rappels qui en découlent
 * sans que `Prescription` ait besoin de le savoir.
 */
class ScheduleMedicationReminders
{
    public function __construct(private readonly ReminderService $reminders) {}

    public function handle(PrescriptionSigned $event): void
    {
        $this->reminders->scheduleForPrescription($event->prescription);
    }
}
