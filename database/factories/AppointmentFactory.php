<?php

namespace Database\Factories;

use App\Domains\Practitioner\Enums\AppointmentStatus;
use App\Domains\Practitioner\Enums\AppointmentType;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Appointment> */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'patient_id' => User::factory(),
            'practitioner_id' => User::factory(),
            'type' => AppointmentType::Teleconsultation,
            'status' => AppointmentStatus::Requested,
            'scheduled_at' => now()->addDays(2),
        ];
    }
}
