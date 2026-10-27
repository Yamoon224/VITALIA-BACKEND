<?php

namespace App\Domains\Practitioner\Http\Requests;

use App\Domains\Practitioner\Enums\AppointmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RequestAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'practitioner_id' => ['required', 'integer', 'exists:practitioner_profiles,user_id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'type' => ['required', new Enum(AppointmentType::class)],
        ];
    }
}
