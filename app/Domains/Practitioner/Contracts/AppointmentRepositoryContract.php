<?php

namespace App\Domains\Practitioner\Contracts;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Collection;

interface AppointmentRepositoryContract
{
    public function request(User $patient, User $practitioner, \DateTimeInterface $scheduledAt, string $type): Appointment;

    public function find(int $id): ?Appointment;

    /** @return Collection<int, Appointment> */
    public function forPatient(User $patient): Collection;

    /** @return Collection<int, Appointment> */
    public function forPractitioner(User $practitioner): Collection;

    public function save(Appointment $appointment): Appointment;
}
