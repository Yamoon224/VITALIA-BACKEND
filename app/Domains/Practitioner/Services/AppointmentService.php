<?php

namespace App\Domains\Practitioner\Services;

use App\Domains\Practitioner\Contracts\AppointmentRepositoryContract;
use App\Domains\Practitioner\Enums\AppointmentStatus;
use App\Domains\Practitioner\Exceptions\InvalidAppointmentTransitionException;
use App\Models\Appointment;
use App\Models\User;
use DateTimeInterface;

/**
 * Prise de rendez-vous en ligne et téléconsultation (section 5.11).
 */
class AppointmentService
{
    public function __construct(private readonly AppointmentRepositoryContract $appointments) {}

    public function request(User $patient, User $practitioner, DateTimeInterface $scheduledAt, string $type): Appointment
    {
        return $this->appointments->request($patient, $practitioner, $scheduledAt, $type);
    }

    public function confirm(Appointment $appointment): Appointment
    {
        $this->transition($appointment, AppointmentStatus::Confirmed);

        return $this->appointments->save($appointment);
    }

    public function complete(Appointment $appointment): Appointment
    {
        $this->transition($appointment, AppointmentStatus::Completed);

        return $this->appointments->save($appointment);
    }

    public function cancel(Appointment $appointment): Appointment
    {
        $this->transition($appointment, AppointmentStatus::Cancelled);

        return $this->appointments->save($appointment);
    }

    private function transition(Appointment $appointment, AppointmentStatus $next): void
    {
        if (! $appointment->status->canTransitionTo($next)) {
            throw InvalidAppointmentTransitionException::from($appointment->status, $next);
        }

        $appointment->status = $next;
    }
}
