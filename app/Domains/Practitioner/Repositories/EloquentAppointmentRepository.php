<?php

namespace App\Domains\Practitioner\Repositories;

use App\Domains\Practitioner\Contracts\AppointmentRepositoryContract;
use App\Domains\Practitioner\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Collection;

class EloquentAppointmentRepository implements AppointmentRepositoryContract
{
    public function request(User $patient, User $practitioner, DateTimeInterface $scheduledAt, string $type): Appointment
    {
        return Appointment::query()->create([
            'patient_id' => $patient->id,
            'practitioner_id' => $practitioner->id,
            'type' => $type,
            'status' => AppointmentStatus::Requested,
            'scheduled_at' => $scheduledAt,
        ]);
    }

    public function find(int $id): ?Appointment
    {
        return Appointment::query()->find($id);
    }

    /** @return Collection<int, Appointment> */
    public function forPatient(User $patient): Collection
    {
        return Appointment::query()->where('patient_id', $patient->id)->orderBy('scheduled_at')->get();
    }

    /** @return Collection<int, Appointment> */
    public function forPractitioner(User $practitioner): Collection
    {
        return Appointment::query()->where('practitioner_id', $practitioner->id)->orderBy('scheduled_at')->get();
    }

    public function save(Appointment $appointment): Appointment
    {
        $appointment->save();

        return $appointment;
    }
}
