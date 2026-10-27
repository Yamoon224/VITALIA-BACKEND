<?php

namespace App\Domains\Practitioner\Http\Resources;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Appointment */
class AppointmentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'practitioner_id' => $this->practitioner_id,
            'type' => $this->type,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at,
        ];
    }
}
